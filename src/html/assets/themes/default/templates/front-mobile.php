<div data-role="page" data-add-back-btn="true" id="Gallery1" class="gallery-page" data-theme="c">
	<div data-role="header" class="photoheader" data-theme="c"></div>

	<div data-role="content">	
    <form id="search-photo-form" action="ajax-gallery1.html" method="get">
            <div data-role="fieldcontain" class="center-wrapper">
                <input type="search" name="search-photos" id="search-photos" value="search your photos ..."/>
            </div>
        </form>		
    <ul data-role="listview" data-inset="true">
        <li><a href="/photos/list"><img
        src="<?php getTheme()->asset('image', 'header-navigation-photos.png'); ?>" alt="my photos"
                        class="ui-li-icon">Photos</a></li> 
        <li><a href="#"><img
        src="<?php getTheme()->asset('image', 'header-navigation-tags.png'); ?>" alt="tags"
                        class="ui-li-icon">Tags</a></li>
    </ul> 
	</div>
	<div data-role="footer" data-theme="c">
		<h4>The OpenPhoto Project &#169; 2011</h4>
	</div>
</div>

