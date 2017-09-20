<div class="row">
  <div class="twelve columns">
    <div class="row collapse">
    	<form action="<?php echo home_url( '/' ); ?>" method="get">
	      <div class="large-8 small-8 columns">
	        <input type="text" id="search" placeholder="Search" name="s" value="<?php the_search_query(); ?>" />
	      </div>
	      <div class="large-4 small-4 columns">
	        <button type="submit" id="search-button" class="postfix button">Search</a>
	      </div>
  		</form>
    </div>
  </div>
</div>