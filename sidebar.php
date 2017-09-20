				<div id="primary-sidebar" class="sidebar" role="complementary">

						<?php if ( is_active_sidebar( 'sidebar1' ) ) : ?>
							<div class="widget-container">
								<?php dynamic_sidebar( 'sidebar1' ); ?>
							</div>

						<?php else : ?>

							<!-- This content shows up if there are no widgets defined in the backend. -->
							
							<div class="alert-box">Please activate some Widgets.</div>

						<?php endif; ?>

					</div>

				</div>