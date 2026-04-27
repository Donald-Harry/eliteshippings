<?php
$c_page = $_SERVER['REQUEST_URI'];
if ($c_page == $nav_config_pt . 'index.php') {
	$index = 'active_color';
} else {
	$index = '';
}

if ($c_page == $nav_config_pt . 'about.php') {
	$about = 'active_color';
} else {
	$about = '';
}

if ($c_page == $nav_config_pt . 'service.php') {
	$service = 'active_color';
} else {
	$service = '';
}


if ($c_page == $nav_config_pt . 'contact.php') {
	$contact = 'active_color';
} else {
	$contact = '';
}

if ($c_page == $nav_config_pt . 'privacy.php') {
	$privacy = 'active_color';
} else {
	$privacy = '';
}

if ($c_page == $nav_config_pt . 'terms.php') {
	$terms = 'active_color';
} else {
	$terms = '';
}

if ($c_page == $nav_config_pt . 'tracking.php') {
	$tracking = 'active_color';
} else {
	$tracking = '';
}


?>
<header class="main_menu home_menu">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-lg-12">
				<nav class="navbar navbar-expand-lg navbar-light">
					<a style="color: navy; font-size: 20px; font-weight: bold;" class="navbar-brand"
						href="../index.html">Admin
						Control</a>
					<button class="navbar-toggler" type="button" data-toggle="collapse"
						data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
						aria-expanded="false" aria-label="Toggle navigation">
						<span class="menu_icon"></span>
					</button>
					<div class="collapse navbar-collapse main-menu-item" id="navbarSupportedContent">
						<ul class="navbar-nav">
							<li class="nav-item">
								<a style="color:navy" class="nav-link <?= $index ?>" href="../index.html">Home</a>
							</li>
							<li class="nav-item">
								<a style="color:navy" class="nav-link <?= $tracking ?>"
									href="../tracking.html">Tracking</a>
							</li>



				</nav>
			</div>
		</div>
	</div>
	<div class="search_input" id="search_input_box">
		<div class="container">
			<form class="d-flex justify-content-between search-inner">
				<input type="text" class="form-control" id="search_input" placeholder="Search Here">
				<button type="submit" class="btn"></button>
				<span class="ti-close" id="close_search" title="Close Search"></span>
			</form>
		</div>
	</div>
</header>