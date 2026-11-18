<?php
/**
 * Plugin Name: TopBlogTech Core
 * Description: Site-specific functionality for topblogtech.com (structured data, analytics with Consent Mode,
 *              security hardening, legacy redirects, contact form). Theme-independent so theme updates cannot
 *              remove it. SEO titles/descriptions/canonical/robots/Open Graph are owned by the SiteSEO plugin.
 * Version:     2.0.0
 * Author:      TopBlogTech
 *
 * @package TopBlogTech
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * 1. Site constants
 * ---------------------------------------------------------------------- */
const TBT_GA4_ID         = 'G-SNDLMK8H9E';
const TBT_GSC_TOKEN      = 'hqGQLKOd7hV2OFJhYRwpBaDiI2hc_aXdP3DdLmOjMrg';
const TBT_LOGO_PATH      = '/wp-content/uploads/topblogtech-nav-logo.png';
const TBT_AUTHOR_NAME    = 'Deepak Pandey';

/* -------------------------------------------------------------------------
 * 2. Head output: Search Console verification, GA4 + Consent Mode v2
 * ---------------------------------------------------------------------- */
add_action(
	'wp_head',
	static function () {
		echo '<meta name="google-site-verification" content="' . esc_attr( TBT_GSC_TOKEN ) . '" />' . "\n";
	},
	1
);

/**
 * GA4 with Google Consent Mode v2.
 * Visitors in the EEA, UK and Switzerland default to "denied" for all storage
 * (no analytics/ad cookies are set; GA receives cookieless pings only) until a
 * Google-certified CMP is installed to collect consent and call gtag('consent','update').
 * Everyone else defaults to granted analytics and denied ad storage (no ads are served yet).
 */
add_action(
	'wp_head',
	static function () {
		if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
			return; // Do not pollute analytics with editor/admin traffic.
		}
		$regions = array( 'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 'IS', 'IE', 'IT', 'LV', 'LI', 'LT', 'LU', 'MT', 'NL', 'NO', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'GB', 'CH' );
		?>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('consent', 'default', {ad_storage:'denied', ad_user_data:'denied', ad_personalization:'denied', analytics_storage:'denied', region:<?php echo wp_json_encode( $regions ); ?>});
gtag('consent', 'default', {ad_storage:'denied', ad_user_data:'denied', ad_personalization:'denied', analytics_storage:'granted'});
gtag('js', new Date());
gtag('config', '<?php echo esc_js( TBT_GA4_ID ); ?>');
</script>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo rawurlencode( TBT_GA4_ID ); ?>"></script>
		<?php
	},
	2
);

/* -------------------------------------------------------------------------
 * 3. Structured data (JSON-LD)
 *    SiteSEO outputs the Organization node (we only fill in its empty name/logo).
 *    We add BlogPosting + BreadcrumbList on posts, which SiteSEO free does not.
 * ---------------------------------------------------------------------- */
add_filter(
	'siteseo_social_graph_json_ld',
	static function ( $json ) {
		$json['name'] = get_bloginfo( 'name' );
		$json['url']  = home_url( '/' );
		if ( empty( $json['logo']['url'] ) ) {
			$json['logo'] = array(
				'@type' => 'ImageObject',
				'@id'   => home_url( '/#logo' ),
				'url'   => home_url( TBT_LOGO_PATH ),
			);
		}
		$json['description']          = get_bloginfo( 'description' );
		$json['publishingPrinciples'] = home_url( '/editorial-policy/' );
		return $json;
	}
);

/** Author name shown in bylines/schema (single-author site). */
function tbt_author_name() {
	return TBT_AUTHOR_NAME;
}
add_filter( 'the_author', 'tbt_author_name' );
add_filter( 'get_the_author_display_name', 'tbt_author_name' );

add_action(
	'wp_head',
	static function () {
		if ( ! is_singular( 'post' ) ) {
			return;
		}
		$post      = get_queried_object();
		$url       = get_permalink( $post );
		$thumb     = get_the_post_thumbnail_url( $post, 'full' );
		$cats      = get_the_category( $post->ID );
		$cat       = $cats ? $cats[0] : null;
		$published = get_the_date( 'c', $post );
		$modified  = get_the_modified_date( 'c', $post );
		if ( strtotime( $modified ) < strtotime( $published ) ) {
			$modified = $published; // Never emit dateModified earlier than datePublished.
		}

		$graph = array(
			array(
				'@type'            => 'BlogPosting',
				'@id'              => $url . '#article',
				'headline'         => wp_strip_all_tags( get_the_title( $post ) ),
				'description'      => wp_strip_all_tags( get_the_excerpt( $post ) ),
				'datePublished'    => $published,
				'dateModified'     => $modified,
				'inLanguage'       => 'en-US',
				'mainEntityOfPage' => $url,
				'author'           => array(
					'@type' => 'Person',
					'name'  => TBT_AUTHOR_NAME,
					'url'   => home_url( '/about-us/' ),
				),
				'publisher'        => array(
					'@type' => 'Organization',
					'@id'   => trailingslashit( home_url() ) . '#Organization',
					'name'  => get_bloginfo( 'name' ),
					'logo'  => array(
						'@type' => 'ImageObject',
						'url'   => home_url( TBT_LOGO_PATH ),
					),
				),
			),
			array(
				'@type'           => 'BreadcrumbList',
				'@id'             => $url . '#breadcrumb',
				'itemListElement' => array_values(
					array_filter(
						array(
							array(
								'@type'    => 'ListItem',
								'position' => 1,
								'name'     => 'Home',
								'item'     => home_url( '/' ),
							),
							$cat ? array(
								'@type'    => 'ListItem',
								'position' => 2,
								'name'     => $cat->name,
								'item'     => get_category_link( $cat ),
							) : null,
							array(
								'@type'    => 'ListItem',
								'position' => $cat ? 3 : 2,
								'name'     => wp_strip_all_tags( get_the_title( $post ) ),
							),
						)
					)
				),
			),
		);
		if ( $thumb ) {
			$graph[0]['image'] = $thumb;
		}
		if ( $cat ) {
			$graph[0]['articleSection'] = $cat->name;
		}

		echo '<script type="application/ld+json">' . wp_json_encode(
			array(
				'@context' => 'https://schema.org',
				'@graph'   => $graph,
			),
			JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
		) . "</script>\n";
	},
	20
);

/* -------------------------------------------------------------------------
 * 4. Security hardening
 * ---------------------------------------------------------------------- */
add_filter( 'xmlrpc_enabled', '__return_false' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

// Block username enumeration via ?author=N (front end only).
add_action(
	'template_redirect',
	static function () {
		if ( isset( $_GET['author'] ) && ! is_admin() ) { // phpcs:ignore WordPress.Security.NonceVerification
			wp_safe_redirect( home_url( '/' ), 301 );
			exit;
		}
	},
	0
);

// Hide the users REST endpoints from anonymous visitors (block editor is authenticated, unaffected).
add_filter(
	'rest_endpoints',
	static function ( $endpoints ) {
		if ( ! is_user_logged_in() ) {
			unset( $endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
		}
		return $endpoints;
	}
);

// Comments are not used on this site: close them everywhere and stop pingbacks.
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
add_filter( 'comments_array', '__return_empty_array', 20 );

/* -------------------------------------------------------------------------
 * 4b. Front-end performance: drop assets that no page uses
 * ---------------------------------------------------------------------- */
add_action(
	'wp_enqueue_scripts',
	static function () {
		// FormLayer enqueues CSS + JS (+ jQuery, jQuery Migrate, Dashicons) on every page even
		// when no FormLayer form is embedded. Keep them only where its shortcode is present.
		$post         = get_post();
		$has_formlayer = is_singular() && $post && has_shortcode( $post->post_content, 'formlayer' );
		if ( ! $has_formlayer ) {
			wp_dequeue_style( 'formlayer-frontend' );
			wp_dequeue_script( 'formlayer-frontend' );
			if ( ! is_admin_bar_showing() ) {
				wp_dequeue_style( 'dashicons' );
			}
		}
	},
	100
);

// Emoji detection script/styles (modern browsers render emoji natively).
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
add_action(
	'wp_enqueue_scripts',
	static function () {
		wp_dequeue_style( 'wp-emoji-styles' );
	},
	100
);
// Comment feeds advertise a feature the site does not use.
add_filter( 'feed_links_show_comments_feed', '__return_false' );
add_action(
	'wp',
	static function () {
		if ( is_singular() ) {
			remove_action( 'wp_head', 'feed_links_extra', 3 );
		}
	}
);

/* -------------------------------------------------------------------------
 * 5. Legacy URL redirects (PHP fallback; .htaccess handles these first on Apache)
 * ---------------------------------------------------------------------- */
function tbt_legacy_redirect_map() {
	return array(
		'/sample-page/'                                               => '/',
		'/how-to-start-a-tech-finance-blog-2024/'                     => '/how-to-start-a-tech-finance-blog-modern/',
		'/best-passive-income-ideas-2024/'                            => '/best-passive-income-ideas-modern/',
		'/top-7-ai-tools-to-make-money-online-2024/'                  => '/top-7-ai-tools-to-make-money-online-modern/',
		'/high-yield-savings-accounts-vs-index-funds-2024/'           => '/high-yield-savings-accounts-vs-index-funds-modern/',
		'/high-income-digital-skills-to-learn-2024/'                  => '/high-income-digital-skills-to-learn-modern/',
		'/dividend-growth-investing-passive-income-portfolio-2024/'   => '/dividend-growth-investing-passive-income-portfolio-modern/',
		'/best-password-managers-1password-bitwarden-dashlane-2024/'  => '/best-password-managers-1password-bitwarden-dashlane-modern/',
		'/n8n-vs-make-vs-zapier-ai-automation-comparison-2024/'       => '/n8n-vs-make-vs-zapier-ai-automation-comparison-modern/',
		'/best-vpn-services-nordvpn-expressvpn-surfshark-2024/'       => '/best-vpn-services-nordvpn-expressvpn-surfshark-modern/',
		// Wrong slugs that were linked internally (returned 404 until 2026-11-18).
		'/highest-cpc-niches-for-google-adsense/'                     => '/top-highest-cpc-niches-google-adsense-blogging/',
		'/create-sell-digital-products-online-with-ai/'               => '/create-sell-digital-products-online-ai-blueprint/',
		'/best-web-hosting-for-wordpress-speed-tests/'                => '/best-web-hosting-wordpress-speed-tests-comparison/',
		'/no-code-ai-automation-agency-blueprint/'                    => '/how-to-start-ai-automation-agency-aaa-blueprint/',
		'/best-ai-seo-tools-to-rank-on-google/'                       => '/best-ai-seo-tools-rank-google-tested/',
		'/how-to-grow-your-audience-on-x-twitter/'                    => '/how-to-grow-your-audience-on-x-com/',
		'/faceless-youtube-channel-with-ai-blueprint/'                => '/how-to-build-a-high-rpm-faceless-youtube-channel-ai/',
		// Category consolidation.
		'/category/linkedin-growth/'                                  => '/category/youtube-growth/',
		'/category/self-growth/'                                      => '/category/blogging/',
		'/category/instagram/'                                        => '/category/youtube-growth/',
		'/category/uncategorized/'                                    => '/category/blogging/',
		'/category/affiliate-marketing/'                              => '/category/online-money/',
		'/category/x-twitter/'                                        => '/category/youtube-growth/',
		'/category/passive-income/'                                   => '/category/online-money/',
		'/category/tech-reviews/'                                     => '/category/tech-ai/',
		'/category/ai-tools/'                                         => '/category/tech-ai/',
		'/category/technology/'                                       => '/category/tech-ai/',
		'/category/finance-wealth/'                                   => '/category/finance/',
	);
}

add_action(
	'template_redirect',
	static function () {
		if ( ! is_404() ) {
			return;
		}
		$path = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ) : '';
		$path = trailingslashit( '/' . ltrim( $path, '/' ) );
		$map  = tbt_legacy_redirect_map();
		if ( isset( $map[ $path ] ) ) {
			wp_safe_redirect( home_url( $map[ $path ] ), 301 );
			exit;
		}
	},
	1
);

/* -------------------------------------------------------------------------
 * 6. Contact form  [tbt_contact_form]
 *    Sends the message to the site admin email via wp_mail(). Nothing is stored
 *    in the database. Protected by nonce, honeypot, minimum fill time and a
 *    per-IP rate limit.
 * ---------------------------------------------------------------------- */
function tbt_contact_form_shortcode() {
	$status = isset( $_GET['tbt_contact'] ) ? sanitize_key( wp_unslash( $_GET['tbt_contact'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$notice = '';
	if ( 'sent' === $status ) {
		$notice = '<p class="tbt-form-notice tbt-form-ok" role="status">Thank you — your message has been sent. We aim to reply within a few business days.</p>';
	} elseif ( 'invalid' === $status ) {
		$notice = '<p class="tbt-form-notice tbt-form-err" role="alert">Please enter your name, a valid email address and a message of at least 20 characters.</p>';
	} elseif ( 'error' === $status ) {
		$notice = '<p class="tbt-form-notice tbt-form-err" role="alert">Sorry, the message could not be sent right now. Please email contact@topblogtech.com directly.</p>';
	} elseif ( 'wait' === $status ) {
		$notice = '<p class="tbt-form-notice tbt-form-err" role="alert">Too many messages were sent from your connection. Please try again later or email us directly.</p>';
	}

	ob_start();
	?>
<form class="tbt-contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
	<?php echo $notice; // phpcs:ignore WordPress.Security.EscapeOutput -- static markup above. ?>
	<input type="hidden" name="action" value="tbt_contact">
	<input type="hidden" name="tbt_ts" value="<?php echo esc_attr( time() ); ?>">
	<?php wp_nonce_field( 'tbt_contact', 'tbt_nonce' ); ?>
	<p class="tbt-hp" aria-hidden="true" style="position:absolute;left:-9999px;">
		<label>Leave this field empty <input type="text" name="tbt_website" tabindex="-1" autocomplete="off"></label>
	</p>
	<p><label for="tbt-name">Name</label><br>
		<input id="tbt-name" type="text" name="tbt_name" required maxlength="100" autocomplete="name"></p>
	<p><label for="tbt-email">Email</label><br>
		<input id="tbt-email" type="email" name="tbt_email" required maxlength="190" autocomplete="email"></p>
	<p><label for="tbt-subject">Subject</label><br>
		<input id="tbt-subject" type="text" name="tbt_subject" maxlength="150"></p>
	<p><label for="tbt-message">Message</label><br>
		<textarea id="tbt-message" name="tbt_message" required minlength="20" maxlength="5000" rows="7"></textarea></p>
	<p class="tbt-form-privacy">Your name, email and message are sent to the site owner by email and are not stored on this website. See our <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>">Privacy Policy</a>.</p>
	<p><button type="submit" class="tbt-btn-submit">Send message</button></p>
</form>
	<?php
	return ob_get_clean();
}
add_shortcode( 'tbt_contact_form', 'tbt_contact_form_shortcode' );

function tbt_contact_form_handle() {
	$back = wp_get_referer() ? wp_get_referer() : home_url( '/contact-us/' );
	$back = remove_query_arg( 'tbt_contact', $back );
	$go   = static function ( $code ) use ( $back ) {
		wp_safe_redirect( add_query_arg( 'tbt_contact', $code, $back ) . '#tbt-name', 303 );
		exit;
	};

	if ( ! isset( $_POST['tbt_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tbt_nonce'] ) ), 'tbt_contact' ) ) {
		$go( 'error' );
	}
	// Honeypot or submitted faster than 3 seconds → silently pretend success (bot).
	$ts = isset( $_POST['tbt_ts'] ) ? (int) $_POST['tbt_ts'] : 0;
	if ( ! empty( $_POST['tbt_website'] ) || ( time() - $ts ) < 3 ) {
		$go( 'sent' );
	}

	$name    = sanitize_text_field( wp_unslash( $_POST['tbt_name'] ?? '' ) );
	$email   = sanitize_email( wp_unslash( $_POST['tbt_email'] ?? '' ) );
	$subject = sanitize_text_field( wp_unslash( $_POST['tbt_subject'] ?? '' ) );
	$message = sanitize_textarea_field( wp_unslash( $_POST['tbt_message'] ?? '' ) );

	if ( '' === $name || ! is_email( $email ) || strlen( $message ) < 20 ) {
		$go( 'invalid' );
	}

	// Behind Cloudflare REMOTE_ADDR is a shared edge IP; use the visitor IP Cloudflare passes.
	$ip_src = ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : ( $_SERVER['REMOTE_ADDR'] ?? '' );
	$ip     = filter_var( wp_unslash( $ip_src ), FILTER_VALIDATE_IP ) ? wp_unslash( $ip_src ) : 'unknown';
	$key = 'tbt_cf_' . md5( $ip );
	$n   = (int) get_transient( $key );
	if ( $n >= 3 ) {
		$go( 'wait' );
	}
	set_transient( $key, $n + 1, HOUR_IN_SECONDS );

	$to   = get_option( 'admin_email' );
	$subj = '[TopBlogTech contact] ' . ( '' !== $subject ? $subject : 'Message from ' . $name );
	$body = "Name: $name\nEmail: $email\n\n$message\n\n--\nSent from " . home_url( '/contact-us/' );
	$hdrs = array( 'Reply-To: ' . $name . ' <' . $email . '>' );

	$go( wp_mail( $to, $subj, $body, $hdrs ) ? 'sent' : 'error' );
}
add_action( 'admin_post_nopriv_tbt_contact', 'tbt_contact_form_handle' );
add_action( 'admin_post_tbt_contact', 'tbt_contact_form_handle' );
