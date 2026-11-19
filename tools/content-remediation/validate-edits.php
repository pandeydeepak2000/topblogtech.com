<?php
/**
 * Offline validator for editorial edit files (no WordPress needed).
 *
 * Usage: php validate-edits.php <stage1-dir> [ID ...]
 *   <stage1-dir> holds <ID>.html files containing Stage-1-cleaned post_content.
 *
 * Checks: JSON validity, every edit applies exactly once, block comment
 * balance is preserved, and the edited result is idempotent on re-run.
 */
if ( PHP_SAPI !== 'cli' ) {
	http_response_code( 404 );
	exit;
}
require __DIR__ . '/lib.php';

$dir = rtrim( $argv[1] ?? '', '/\\' );
if ( ! is_dir( $dir ) ) {
	fwrite( STDERR, "Usage: php validate-edits.php <stage1-dir> [ID ...]\n" );
	exit( 2 );
}
$ids = array_slice( $argv, 2 );
if ( ! $ids ) {
	foreach ( glob( __DIR__ . '/edits/*.json' ) as $f ) {
		$ids[] = basename( $f, '.json' );
	}
}

function tbt_block_balance( $s ) {
	preg_match_all( '#<!-- wp:([a-z0-9/-]+)(?:\s[^>]*?)?(/)?-->#', $s, $open, PREG_SET_ORDER );
	preg_match_all( '#<!-- /wp:([a-z0-9/-]+) -->#', $s, $close );
	$o = array();
	foreach ( $open as $m ) {
		if ( empty( $m[2] ) ) {
			$o[ $m[1] ] = ( $o[ $m[1] ] ?? 0 ) + 1;
		}
	}
	$c = array_count_values( $close[1] );
	ksort( $o );
	ksort( $c );
	return $o == $c ? true : array( 'open' => $o, 'close' => $c );
}

$fail = 0;
foreach ( $ids as $id ) {
	$src = "$dir/$id.html";
	$ef  = __DIR__ . "/edits/$id.json";
	if ( ! is_file( $src ) || ! is_file( $ef ) ) {
		echo "[$id] missing stage1 or edits file\n";
		$fail++;
		continue;
	}
	$spec = json_decode( file_get_contents( $ef ), true );
	if ( ! is_array( $spec ) ) {
		echo "[$id] INVALID JSON: " . json_last_error_msg() . "\n";
		$fail++;
		continue;
	}
	$content          = file_get_contents( $src );
	list( $new, $repf, $bad ) = tbt_apply_spec( $content, $spec );
	list( $again, $rep2f, $bad2 ) = tbt_apply_spec( $new, $spec );
	$rep    = tbt_flatten_report( $repf );
	$rep2   = tbt_flatten_report( $rep2f );
	$counts = array_count_values( $rep );
	$bal    = tbt_block_balance( $new );
	$idem   = ( $again === $new ) && ! $bad2;
	// Production scenario: revision 1 already live, revision 2 must still converge to the same result.
	if ( ! empty( $spec['edits_r2'] ) ) {
		list( $x1 ) = tbt_apply_edits( $content, $spec['edits'] ?? array() );
		list( $fromx1, , $badx1 ) = tbt_apply_spec( $x1, $spec );
		if ( $badx1 || $fromx1 !== $new ) {
			$idem = false;
			echo "    r2-from-live-r1: FAILED
";
		} else {
			echo "    r2-from-live-r1: OK
";
		}
	}
	printf(
		"[%s] edits=%d %s balance=%s idempotent=%s words %d->%d\n",
		$id,
		count( $rep ),
		json_encode( $counts ),
		true === $bal ? 'OK' : 'BROKEN',
		$idem ? 'Y' : 'N',
		str_word_count( strip_tags( $content ) ),
		str_word_count( strip_tags( $new ) )
	);
	foreach ( $rep as $i => $r ) {
		if ( 'applied' !== $r ) {
			echo "    edit #$i: $r :: " . substr( str_replace( "\n", '\n', ( array_merge( $spec['edits'] ?? array(), $spec['edits_r2'] ?? array() )[ $i ]['find'] ?? '' ) ), 0, 110 ) . "\n";
		}
	}
	if ( true !== $bal ) {
		echo '    ' . json_encode( $bal ) . "\n";
	}
	if ( $bad || true !== $bal || ! $idem ) {
		$fail++;
	}
}
echo $fail ? "FAILED: $fail\n" : "ALL OK\n";
exit( $fail ? 1 : 0 );
