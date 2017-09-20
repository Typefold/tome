				<div id="footer-sidebar" class="sidebar row" role="complementary">

					<div class="large-12 columns">
						<?php if ( is_active_sidebar( 'sidebar2' ) ) : ?>
							<ul class="large-block-grid-4">
								<?php dynamic_sidebar( 'sidebar2' ); ?>
							</ul>

						<?php else : ?>

							<!-- This content shows up if there are no widgets defined in the backend. -->
							
							<div class="alert-box">Please activate some Widgets.</div>

						<?php endif; ?>
					</div>

				</div>