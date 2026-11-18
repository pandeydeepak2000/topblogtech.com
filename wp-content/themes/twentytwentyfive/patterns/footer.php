<?php
/**
 * Title: Footer
 * Slug: twentytwentyfive/footer
 * Categories: footer
 * Block Types: core/template-part/footer
 * Description: TopBlogTech Premium Multi-Column Footer with Newsletter and Brand Links.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

$home_url = home_url( '/' );
?>
<!-- wp:html -->
<footer class="tbt-footer">
	<!-- Newsletter Section -->
	<div class="tbt-footer-newsletter-wrap">
		<div class="tbt-footer-newsletter-box">
			<div class="tbt-newsletter-text">
				<span class="tbt-newsletter-badge">✉️ QUESTIONS &amp; CORRECTIONS</span>
				<p class="tbt-newsletter-title" style="color: #ffffff !important;">Spotted an error or have a question?</p>
				<p class="tbt-newsletter-desc" style="color: #cbd5e1 !important;">Guides are updated when platforms, policies or prices change. If something looks outdated or wrong, let us know and we will review it.</p>
			</div>
			<div class="tbt-newsletter-form">
				<a href="<?php echo esc_url( $home_url . 'contact-us/' ); ?>" class="tbt-btn-subscribe" style="display:inline-block; text-align:center; text-decoration:none;">Contact TopBlogTech →</a>
				<p class="tbt-form-subtext" style="color: #94a3b8 !important;">Email: <a href="mailto:contact@topblogtech.com" style="color: #60a5fa; text-decoration: underline;">contact@topblogtech.com</a></p>
			</div>
		</div>
	</div>

	<!-- Main Footer Columns -->
	<div class="tbt-footer-main">
		<div class="tbt-footer-grid">
			<!-- Col 1: Brand -->
			<div class="tbt-footer-col tbt-footer-col-brand">
				<a href="<?php echo esc_url( $home_url ); ?>" class="tbt-footer-logo-link">
					<picture>
						<source type="image/webp" srcset="<?php echo esc_url( home_url( '/wp-content/uploads/topblogtech-nav-logo-108h.webp' ) ); ?>">
						<img src="<?php echo esc_url( home_url( '/wp-content/uploads/topblogtech-nav-logo-108h.png' ) ); ?>" alt="TopBlogTech home" class="tbt-footer-logo" width="183" height="108" loading="lazy" decoding="async" />
					</picture>
				</a>
				<p class="tbt-footer-tagline"><strong>TopBlogTech.com</strong> is an independent blog with research-based guides on blogging, SEO, website monetization, creator tools and personal finance basics.</p>
			</div>

			<!-- Col 2: Core Niches -->
			<div class="tbt-footer-col">
				<h2 class="tbt-footer-heading">Topics</h2>
				<ul class="tbt-footer-links">
					<li><a href="<?php echo esc_url( $home_url . 'category/blogging/' ); ?>">Blogging Mastery</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'category/online-money/' ); ?>">Online Money Models</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'category/youtube-growth/' ); ?>">YouTube &amp; Social</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'category/google-adsense/' ); ?>">Google AdSense</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'category/tech-ai/' ); ?>">Tech &amp; AI Tools</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'category/finance/' ); ?>">Finance &amp; Wealth</a></li>
				</ul>
			</div>

			<!-- Col 3: Popular Guides -->
			<div class="tbt-footer-col">
				<h2 class="tbt-footer-heading">Popular Guides</h2>
				<ul class="tbt-footer-links">
					<li><a href="<?php echo esc_url( $home_url . 'how-to-start-a-tech-finance-blog-modern/' ); ?>">Start a Tech &amp; Finance Blog</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'top-5-realistic-ways-to-earn-money-online/' ); ?>">Realistic Ways to Earn Online</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'youtube-algorithm-secrets-to-get-more-views/' ); ?>">YouTube Algorithm Mechanics</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'getting-google-adsense-approval-fast/' ); ?>">AdSense Eligibility Checklist</a></li>
				</ul>
			</div>

			<!-- Col 4: Platform & Trust -->
			<div class="tbt-footer-col">
				<h2 class="tbt-footer-heading">Editorial &amp; Legal</h2>
				<ul class="tbt-footer-links">
					<li><a href="<?php echo esc_url( $home_url . 'about-us/' ); ?>">About TopBlogTech</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'editorial-policy/' ); ?>">Editorial Policy</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'corrections-policy/' ); ?>">Content Corrections</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'privacy-policy/' ); ?>">Privacy Policy</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'affiliate-disclosure/' ); ?>">Affiliate Disclosure</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'terms-of-service/' ); ?>">Terms of Service</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'contact-us/' ); ?>">Contact Us</a></li>
				</ul>
			</div>
		</div>

		<!-- Footer Bottom Copyright -->
		<div class="tbt-footer-bottom">
			<div class="tbt-footer-bottom-inner">
				<p class="tbt-copyright">© <?php echo date( 'Y' ); ?> <strong>TopBlogTech.com</strong>. All rights reserved.</p>
				<div class="tbt-footer-bottom-links">
					<a href="<?php echo esc_url( $home_url ); ?>">Home</a>
					<span>•</span>
					<a href="<?php echo esc_url( $home_url . 'about-us/' ); ?>">About</a>
					<span>•</span>
					<a href="<?php echo esc_url( $home_url . 'editorial-policy/' ); ?>">Editorial</a>
					<span>•</span>
					<a href="<?php echo esc_url( $home_url . 'privacy-policy/' ); ?>">Privacy</a>
					<span>•</span>
					<a href="<?php echo esc_url( $home_url . 'affiliate-disclosure/' ); ?>">Disclosure</a>
					<span>•</span>
					<a href="<?php echo esc_url( $home_url . 'contact-us/' ); ?>">Contact</a>
					<span>•</span>
					<a href="<?php echo esc_url( $home_url . 'sitemaps.xml' ); ?>">Sitemap</a>
				</div>
			</div>
		</div>
	</div>
</footer>
<!-- /wp:html -->
