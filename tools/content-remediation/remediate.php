<?php
/**
 * TopBlogTech database remediation runner (CLI only, idempotent).
 *
 *   php tools/content-remediation/remediate.php                 # DRY RUN (default) — shows what would change
 *   php tools/content-remediation/remediate.php --apply         # writes changes (after a JSON backup)
 *   php tools/content-remediation/remediate.php --apply --only=14,16
 *   php tools/content-remediation/remediate.php --rollback=/path/to/backup.json [--apply]
 *
 * Options:
 *   --backup-dir=PATH   where backups are written (default: $HOME/tbt-remediation-backups — outside webroot)
 *   --skip-site         only process posts/pages, skip site options / category cleanup
 *
 * What it does:
 *   Posts & pages: Stage 1 (unescape literal \n \' \", remove 78 broken placeholder images,
 *   fix 7 wrong internal slugs) + Stage 2 reviewed editorial edits from edits/<ID>.json,
 *   optional title/excerpt fixes, closes comments/pings. Post IDs, slugs, dates published,
 *   authors, featured images and categories are never changed. post_modified is set to now.
 *   Site: closes default comments, SiteSEO homepage title/description, noindex thin tag archives,
 *   large Twitter cards, default category -> Blogging, deletes empty legacy categories that
 *   already 301-redirect (removes 11 redirecting URLs from the category sitemap).
 */
if ( PHP_SAPI !== 'cli' ) {
	http_response_code( 404 );
	exit;
}

$opts = getopt( '', array( 'apply', 'only:', 'backup-dir:', 'rollback:', 'skip-site', 'wp-root:' ) );
$apply = isset( $opts['apply'] );

$wp_root = isset( $opts['wp-root'] ) ? rtrim( $opts['wp-root'], '/\\' ) : dirname( __DIR__, 2 );
if ( ! is_file( $wp_root . '/wp-load.php' ) ) {
	fwrite( STDERR, "wp-load.php not found in $wp_root (use --wp-root=PATH)\n" );
	exit( 2 );
}
define( 'WP_USE_THEMES', false );
$_SERVER['HTTP_HOST']   = $_SERVER['HTTP_HOST'] ?? 'topblogtech.com';
$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/';
require $wp_root . '/wp-load.php';
require __DIR__ . '/lib.php';

global $wpdb;

$home       = getenv( 'HOME' ) ? getenv( 'HOME' ) : ( getenv( 'USERPROFILE' ) ? getenv( 'USERPROFILE' ) : sys_get_temp_dir() );
$backup_dir = isset( $opts['backup-dir'] ) ? rtrim( $opts['backup-dir'], '/\\' ) : $home . '/tbt-remediation-backups';

function tbt_out( $s ) {
	echo $s . "\n";
}

tbt_out( '== TopBlogTech remediation — ' . ( $apply ? 'APPLY MODE' : 'DRY RUN (no changes written; add --apply)' ) );
tbt_out( 'Site: ' . home_url() . ' | DB: ' . DB_NAME . ' | prefix: ' . $wpdb->prefix );

/* --------------------------------------------------------------------------
 * Rollback
 * ----------------------------------------------------------------------- */
if ( isset( $opts['rollback'] ) ) {
	$b = json_decode( (string) file_get_contents( $opts['rollback'] ), true );
	if ( ! is_array( $b ) || empty( $b['posts'] ) ) {
		fwrite( STDERR, "Invalid backup file\n" );
		exit( 2 );
	}
	foreach ( $b['posts'] as $p ) {
		tbt_out( "rollback post {$p['ID']}" );
		if ( $apply ) {
			$wpdb->update(
				$wpdb->posts,
				array(
					'post_content'      => $p['post_content'],
					'post_title'        => $p['post_title'],
					'post_excerpt'      => $p['post_excerpt'],
					'post_modified'     => $p['post_modified'],
					'post_modified_gmt' => $p['post_modified_gmt'],
					'comment_status'    => $p['comment_status'],
					'ping_status'       => $p['ping_status'],
				),
				array( 'ID' => (int) $p['ID'] )
			);
			foreach ( ( $p['meta'] ?? array() ) as $k => $v ) {
				null === $v ? delete_post_meta( $p['ID'], $k ) : update_post_meta( $p['ID'], $k, $v );
			}
			clean_post_cache( (int) $p['ID'] );
		}
	}
	foreach ( ( $b['options'] ?? array() ) as $k => $v ) {
		tbt_out( "rollback option $k" );
		if ( $apply ) {
			update_option( $k, $v );
		}
	}
	foreach ( ( $b['deleted_terms'] ?? array() ) as $t ) {
		tbt_out( "re-create category {$t['slug']}" );
		if ( $apply && ! term_exists( $t['slug'], 'category' ) ) {
			wp_insert_term( $t['name'], 'category', array( 'slug' => $t['slug'], 'description' => $t['description'] ) );
		}
	}
	tbt_out( $apply ? 'Rollback applied.' : 'Dry run — add --apply to restore.' );
	exit( 0 );
}

/* --------------------------------------------------------------------------
 * Posts & pages
 * ----------------------------------------------------------------------- */
$only = isset( $opts['only'] ) ? array_map( 'intval', explode( ',', $opts['only'] ) ) : array();
$ids  = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_status='publish' AND post_type IN ('post','page') ORDER BY ID" );
if ( $only ) {
	$ids = array_values( array_intersect( $ids, $only ) );
}

$backup  = array(
	'created' => gmdate( 'c' ),
	'site'    => home_url(),
	'posts'   => array(),
	'options' => array(),
	'deleted_terms' => array(),
);
$changes = array();
$blocked = array();

foreach ( $ids as $id ) {
	$p = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->posts} WHERE ID=%d", $id ), ARRAY_A );

	$spec = array();
	$ef   = __DIR__ . "/edits/$id.json";
	if ( is_file( $ef ) ) {
		$spec = json_decode( file_get_contents( $ef ), true );
		if ( ! is_array( $spec ) ) {
			$blocked[] = "$id (invalid JSON)";
			continue;
		}
	}

	$content           = tbt_stage1( $p['post_content'] );
	list( $content, $rep ) = tbt_apply_edits( $content, $spec['edits'] ?? array() );
	if ( tbt_report_has_failure( $rep ) ) {
		$blocked[] = "$id (" . json_encode( array_count_values( $rep ) ) . ')';
		tbt_out( sprintf( '  [%d] BLOCKED — editorial edits do not match current content: %s', $id, json_encode( $rep ) ) );
		continue;
	}

	$title   = ! empty( $spec['title'] ) ? $spec['title'] : $p['post_title'];
	$excerpt = isset( $spec['excerpt'] ) && null !== $spec['excerpt'] ? $spec['excerpt'] : tbt_unescape( $p['post_excerpt'] );

	$row = array();
	if ( $content !== $p['post_content'] ) {
		$row['post_content'] = $content;
	}
	if ( $title !== $p['post_title'] ) {
		$row['post_title'] = $title;
	}
	if ( $excerpt !== $p['post_excerpt'] ) {
		$row['post_excerpt'] = $excerpt;
	}
	if ( 'closed' !== $p['comment_status'] ) {
		$row['comment_status'] = 'closed';
	}
	if ( 'closed' !== $p['ping_status'] ) {
		$row['ping_status'] = 'closed';
	}

	// SiteSEO per-post description: keep in sync with the corrected excerpt where one exists.
	$meta_changes = array();
	$seo_desc     = get_post_meta( $id, '_siteseo_titles_desc', true );
	if ( '' !== $seo_desc && isset( $spec['excerpt'] ) && $spec['excerpt'] && $seo_desc !== $spec['excerpt'] ) {
		$meta_changes['_siteseo_titles_desc'] = array( $seo_desc, $spec['excerpt'] );
	}

	if ( ! $row && ! $meta_changes ) {
		tbt_out( sprintf( '  [%d] up to date', $id ) );
		continue;
	}

	$applied_edits = count( array_keys( $rep, 'applied', true ) );
	tbt_out(
		sprintf(
			'  [%d] %-58s fields: %s; editorial edits: %d applied, %d already; words %d -> %d',
			$id,
			mb_substr( $p['post_name'], 0, 58 ),
			implode( ',', array_merge( array_keys( $row ), array_keys( $meta_changes ) ) ),
			$applied_edits,
			count( array_keys( $rep, 'already-applied', true ) ),
			str_word_count( wp_strip_all_tags( tbt_unescape( $p['post_content'] ) ) ),
			str_word_count( wp_strip_all_tags( $content ) )
		)
	);

	$backup['posts'][] = array(
		'ID'                => (int) $id,
		'post_content'      => $p['post_content'],
		'post_title'        => $p['post_title'],
		'post_excerpt'      => $p['post_excerpt'],
		'post_modified'     => $p['post_modified'],
		'post_modified_gmt' => $p['post_modified_gmt'],
		'comment_status'    => $p['comment_status'],
		'ping_status'       => $p['ping_status'],
		'meta'              => array_map( static function ( $c ) { return $c[0]; }, $meta_changes ),
	);
	$changes[ $id ] = array( $row, $meta_changes );
}

/* --------------------------------------------------------------------------
 * Site options & taxonomy cleanup
 * ----------------------------------------------------------------------- */
$opt_changes  = array();
$terms_to_del = array();

if ( ! isset( $opts['skip-site'] ) && ! $only ) {
	foreach ( array( 'default_comment_status' => 'closed', 'default_ping_status' => 'closed' ) as $k => $v ) {
		if ( get_option( $k ) !== $v ) {
			$opt_changes[ $k ] = $v;
		}
	}

	$titles = get_option( 'siteseo_titles_option_name', array() );
	if ( is_array( $titles ) ) {
		$new                              = $titles;
		$new['titles_home_site_title']    = 'TopBlogTech – Practical Guides to Blogging, SEO, Online Income & Tech Tools';
		$new['titles_home_site_desc']     = 'Research-based guides on blogging, SEO, Google AdSense, creator tools, online income and personal finance basics — written for beginners and updated when things change.';
		$new['titles_tax_titles']['post_tag']['noindex'] = 1; // 28 tags with 1–2 posts each = thin archives.
		if ( $new !== $titles ) {
			$opt_changes['siteseo_titles_option_name'] = $new;
		}
	}

	$social = get_option( 'siteseo_social_option_name', array() );
	if ( is_array( $social ) ) {
		$new                                 = $social;
		$new['social_twitter_card_img_size'] = 'Large';
		$new['social_knowledge_type']        = 'Organization';
		$new['social_knowledge_name']        = 'TopBlogTech';
		$new['social_knowledge_img']         = home_url( '/wp-content/uploads/topblogtech-nav-logo.png' );
		if ( $new !== $social ) {
			$opt_changes['siteseo_social_option_name'] = $new;
		}
	}

	$blogging = get_term_by( 'slug', 'blogging', 'category' );
	if ( $blogging && (int) get_option( 'default_category' ) !== (int) $blogging->term_id ) {
		$opt_changes['default_category'] = (int) $blogging->term_id;
	}

	// Empty legacy categories that .htaccess already 301-redirects.
	$legacy = array( 'linkedin-growth', 'self-growth', 'instagram', 'uncategorized', 'affiliate-marketing', 'x-twitter', 'passive-income', 'tech-reviews', 'ai-tools', 'technology', 'finance-wealth' );
	foreach ( $legacy as $slug ) {
		$t = get_term_by( 'slug', $slug, 'category' );
		if ( $t ) {
			$real = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->term_relationships} WHERE term_taxonomy_id=%d", $t->term_taxonomy_id ) );
			if ( 0 === $real ) {
				$terms_to_del[] = $t;
			} else {
				tbt_out( "  category $slug has $real objects — NOT deleting" );
			}
		}
	}

	foreach ( $opt_changes as $k => $v ) {
		$backup['options'][ $k ] = get_option( $k );
		tbt_out( "  option $k -> " . ( is_scalar( $v ) ? $v : 'updated array' ) );
	}
	foreach ( $terms_to_del as $t ) {
		$backup['deleted_terms'][] = array( 'name' => $t->name, 'slug' => $t->slug, 'description' => $t->description );
		tbt_out( "  delete empty category {$t->slug} (id {$t->term_id})" );
	}
}

tbt_out( sprintf( '== Summary: %d posts/pages to update, %d options, %d empty categories, %d blocked', count( $changes ), count( $opt_changes ), count( $terms_to_del ), count( $blocked ) ) );
if ( $blocked ) {
	tbt_out( '   BLOCKED (not written): ' . implode( '; ', $blocked ) );
}

if ( ! $apply ) {
	tbt_out( 'Dry run complete. Nothing was written.' );
	exit( $blocked ? 1 : 0 );
}
if ( ! $changes && ! $opt_changes && ! $terms_to_del ) {
	tbt_out( 'Nothing to do.' );
	exit( 0 );
}

/* --------------------------------------------------------------------------
 * Backup, then write
 * ----------------------------------------------------------------------- */
if ( ! is_dir( $backup_dir ) && ! wp_mkdir_p( $backup_dir ) ) {
	fwrite( STDERR, "Cannot create backup dir $backup_dir — aborting, nothing written.\n" );
	exit( 3 );
}
$bf = $backup_dir . '/tbt-remediation-' . gmdate( 'Ymd-His' ) . '.json';
if ( false === file_put_contents( $bf, wp_json_encode( $backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ) ) {
	fwrite( STDERR, "Cannot write backup $bf — aborting, nothing written.\n" );
	exit( 3 );
}
@chmod( $bf, 0600 );
tbt_out( "Backup written: $bf" );

$now     = current_time( 'mysql' );
$now_gmt = current_time( 'mysql', true );
foreach ( $changes as $id => $c ) {
	list( $row, $meta ) = $c;
	if ( $row ) {
		$row['post_modified']     = $now;
		$row['post_modified_gmt'] = $now_gmt;
		$ok = $wpdb->update( $wpdb->posts, $row, array( 'ID' => (int) $id ) );
		tbt_out( "  [$id] " . ( false === $ok ? 'FAILED: ' . $wpdb->last_error : 'updated' ) );
	}
	foreach ( $meta as $k => $pair ) {
		update_post_meta( $id, $k, $pair[1] );
	}
	clean_post_cache( (int) $id );
}
foreach ( $opt_changes as $k => $v ) {
	update_option( $k, $v );
}
foreach ( $terms_to_del as $t ) {
	$r = wp_delete_term( $t->term_id, 'category' );
	tbt_out( "  deleted category {$t->slug}: " . ( true === $r ? 'ok' : 'FAILED' ) );
}

// Flush caches so the changes are visible immediately.
wp_cache_flush();
if ( class_exists( '\SpeedyCache\Delete' ) && method_exists( '\SpeedyCache\Delete', 'all_cache' ) ) {
	\SpeedyCache\Delete::all_cache();
	tbt_out( 'SpeedyCache page cache purged.' );
} else {
	tbt_out( 'NOTE: purge SpeedyCache manually (wp-admin > SpeedyCache > Delete Cache) and Cloudflare (Purge Everything).' );
}
tbt_out( 'Done. Rollback: php ' . __FILE__ . " --rollback=$bf --apply" );
