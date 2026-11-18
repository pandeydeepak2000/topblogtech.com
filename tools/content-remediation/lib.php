<?php
/**
 * TopBlogTech content remediation — pure helper functions (no WordPress dependency).
 *
 * These functions are deterministic and idempotent: running them on content that
 * has already been cleaned returns the content unchanged.
 */

if ( PHP_SAPI !== 'cli' ) {
	http_response_code( 404 );
	exit;
}

/**
 * Internal links that point at slugs which never existed (all returned HTTP 404
 * on 2026-11-18). Mapped to the real, published slug of the same article.
 */
function tbt_broken_internal_slug_map() {
	return array(
		'highest-cpc-niches-for-google-adsense'       => 'top-highest-cpc-niches-google-adsense-blogging',
		'create-sell-digital-products-online-with-ai' => 'create-sell-digital-products-online-ai-blueprint',
		'best-web-hosting-for-wordpress-speed-tests'  => 'best-web-hosting-wordpress-speed-tests-comparison',
		'no-code-ai-automation-agency-blueprint'      => 'how-to-start-ai-automation-agency-aaa-blueprint',
		'best-ai-seo-tools-to-rank-on-google'         => 'best-ai-seo-tools-rank-google-tested',
		'how-to-grow-your-audience-on-x-twitter'      => 'how-to-grow-your-audience-on-x-com',
		'faceless-youtube-channel-with-ai-blueprint'  => 'how-to-build-a-high-rpm-faceless-youtube-channel-ai',
	);
}

/**
 * Undo the double/triple SQL escaping that left literal "\n", "\'" and '\"'
 * sequences in post content (they render as visible text on the live site).
 * No published post contains a legitimate backslash inside <code>/<pre>
 * (verified 2026-11-18), so a global unescape is safe for this dataset.
 */
function tbt_unescape( $s ) {
	$bs = chr( 92 );
	$prev = null;
	// Collapse repeated escaping layers first ("\\n" -> "\n" -> newline).
	while ( $prev !== $s ) {
		$prev = $s;
		$s    = str_replace(
			array( $bs . $bs . 'n', $bs . $bs . "'", $bs . $bs . '"' ),
			array( $bs . 'n', $bs . "'", $bs . '"' ),
			$s
		);
	}
	return str_replace(
		array( $bs . 'r' . $bs . 'n', $bs . 'n', $bs . "'", $bs . '"' ),
		array( "\n", "\n", "'", '"' ),
		$s
	);
}

/**
 * Remove image blocks whose <img> points to a non-existent "placeholder-*.webp"
 * upload (78 such blocks, every one returned HTTP 404). We do not invent
 * replacement images.
 */
function tbt_remove_placeholder_images( $s ) {
	$s = preg_replace(
		'#<!-- wp:image\b[^>]*-->\s*<figure\b[^>]*>\s*<img\b[^>]*\bsrc="[^"]*/placeholder-[0-9]+-[0-9]+\.webp"[^>]*>\s*(?:<figcaption\b[^>]*>.*?</figcaption>\s*)?</figure>\s*<!-- /wp:image -->\s*#s',
		'',
		$s
	);
	// Any stray bare <figure> placeholder without block comments.
	$s = preg_replace(
		'#<figure\b[^>]*>\s*<img\b[^>]*\bsrc="[^"]*/placeholder-[0-9]+-[0-9]+\.webp"[^>]*>\s*(?:<figcaption\b[^>]*>.*?</figcaption>\s*)?</figure>\s*#s',
		'',
		$s
	);
	return $s;
}

/** Rewrite internal links that point to non-existent slugs. */
function tbt_fix_internal_links( $s ) {
	foreach ( tbt_broken_internal_slug_map() as $bad => $good ) {
		$s = preg_replace(
			'#(href=")https?://(?:www\.)?topblogtech\.com/' . preg_quote( $bad, '#' ) . '/?(["\#])#',
			'$1https://topblogtech.com/' . $good . '/$2',
			$s
		);
	}
	return $s;
}

/** Collapse runs of 3+ blank lines left behind by removed blocks. */
function tbt_tidy_whitespace( $s ) {
	return preg_replace( "/\n{3,}/", "\n\n", $s );
}

/** Stage 1: deterministic mechanical cleanup applied to every post/page. */
function tbt_stage1( $content ) {
	$c = tbt_unescape( $content );
	$c = tbt_remove_placeholder_images( $c );
	$c = tbt_fix_internal_links( $c );
	return tbt_tidy_whitespace( $c );
}

/**
 * Stage 2: apply reviewed editorial find/replace edits.
 *
 * Each edit: { "find": "...", "replace": "...", "reason": "..." , "all": false }
 * Semantics (idempotent):
 *   - find present (exactly once, or any count when all=true) -> replaced
 *   - find absent but replace present -> already applied, skipped
 *   - otherwise -> MISMATCH (reported; caller refuses to write the post)
 *
 * @return array{0:string,1:array} [new content, per-edit report]
 */
function tbt_apply_edits( $content, array $edits ) {
	$report = array();
	foreach ( $edits as $i => $e ) {
		$find    = (string) $e['find'];
		$replace = (string) $e['replace'];
		$all     = ! empty( $e['all'] );
		$n       = ( '' === $find ) ? 0 : substr_count( $content, $find );

		if ( $n === 1 || ( $all && $n > 0 ) ) {
			$content    = str_replace( $find, $replace, $content );
			$report[ $i ] = 'applied';
		} elseif ( $n > 1 ) {
			$report[ $i ] = "AMBIGUOUS ($n matches)";
		} elseif ( '' === $replace || false !== strpos( $content, $replace ) ) {
			$report[ $i ] = 'already-applied';
		} else {
			$report[ $i ] = 'MISMATCH';
		}
	}
	return array( $content, $report );
}

/** True when an edit report contains a failure that must block the write. */
function tbt_report_has_failure( array $report ) {
	foreach ( $report as $r ) {
		if ( 0 === strpos( $r, 'MISMATCH' ) || 0 === strpos( $r, 'AMBIGUOUS' ) ) {
			return true;
		}
	}
	return false;
}
