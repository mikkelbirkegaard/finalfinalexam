<?php
add_filter( 'generate_mobile_menu_label', 'bbh_custom_mobile_menu');

function bbh_custom_mobile_menu() {

	return '<div class="hamburger hamburger--3dx">
				<span class="hamburger-box">
					<span class="hamburger-inner"></span>
				</span>
			</div>';
}
