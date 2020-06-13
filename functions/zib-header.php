<?php
function zib_header()
{
	$layout = _pz('header_layout', '2');
	$m_nav_align = _pz('mobile_navbar_align', 'right');
	$m_layout = _pz('mobile_header_layout', 'center');
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	$show_slide = false;
	if (is_home() && $paged == 1 && _pz('index_slide_s') && _pz('index_slide_position', 'top') == 'header' && _pz('index_slide_src_1')) {
		$show_slide = true;
	}
?>
	<header class="header header-layout-<?php echo $layout;
										echo $show_slide ? ' show-slide' : ''; ?>">
		<nav class="navbar navbar-top <?php echo $m_layout; ?>">
			<div class="container-fluid container-header">
				<?php zib_navbar_header(); ?>
				<div class="collapse navbar-collapse">
					<?php
					if (!wp_is_mobile()) {
						if ($layout != 3) {
							zib_menu_items();
						}
						if ($layout == 2) {
							echo zib_get_menu_search();
						}
						zib_menu_button($layout);
						if ($layout == 3) {
							echo '<div class="navbar-right">';
							zib_menu_items();
							echo '</div>';
						}
					}
					?>
				</div>

			</div>
		</nav>


	</header>

	<?php
	if (wp_is_mobile() || $layout != 2) {
		zib_header_search();
	}
	?>
	<div class="mobile-header">
		<nav <?php echo $m_nav_align != 'top' ? 'nav-touch="' . $m_nav_align . '"' : ''; ?> class="mobile-navbar visible-xs-block scroll-y mini-scrollbar <?php echo $m_nav_align; ?>">
			<?php zib_nav_mobile();
			if (function_exists('dynamic_sidebar')) {
				echo '<div class="mobile-nav-widget">';
				dynamic_sidebar('mobile_nav_fluid');
				echo '</div>';
			}
			?>
		</nav>
		<div class="fixed-body" data-close=".mobile-navbar"></div>
	</div>
	<?php if ($show_slide) {
		zib_index_slide();
	} ?>
<?php }
function zib_menu_button($layout = 1)
{
	$sub = '';
	$li = '';
	$button = '';
	$user_id = get_current_user_id();

	if (_pz('nav_newposts')) {
		$button .= zib_get_write_posts_button('but jb-blue radius');
	}
	$user_id = get_current_user_id();
	if (_pz('nav_pay_vip', true) && (_pz('pay_user_vip_1_s', true) || _pz('pay_user_vip_2_s', true))) {
		$hover_show = '<div class="sub-menu hover-show-con sub-vip-card">' . zibpay_get_vip_card(1) . zibpay_get_vip_card(2).'</div>';
		if ($user_id) {
			if (!zib_get_user_vip_level($user_id)) {
				$vip_button = '<a class="pay-vip but jb-red radius payvip-icon ml10" href="javascript:;">' . zib_svg('vip_1', '0 0 1024 1024', 'em12 mr3').'开通会员</a>';
				$button .= '<span class="hover-show">'.$vip_button.$hover_show.'</span>';
			}
		} else {
			$vip_button = '<a class="signin-loader but jb-red radius payvip-icon ml10" href="javascript:;">' . zib_svg('vip_1', '0 0 1024 1024', 'em12 mr3').'开通会员</a>';
			$button .= '<span class="hover-show">'.$vip_button.$hover_show.'</span>';
		}
	}

	if ($button) {
		$button = '<div class="navbar-form navbar-right navbar-but">' . $button . '</div>';
	}

	$button .= _pz('theme_mode_button', true) ? '<div class="navbar-form navbar-right">
	<a href="javascript:;" class="toggle-theme toggle-radius">' . zib_svg('theme') . '</a>
	</div>' : '';

	$sub = zib_header_user_box();
	$b_b = '
	<div class="navbar-form navbar-right">
		<ul class="list-inline splitters relative">
			<li><a href="javascript:;" class="btn' . ($user_id ? '' : ' signin-loader') . '">' . zib_svg('user', '50 0 924 924') . '</a>
				<ul class="sub-menu">
				' . $sub . '
				</ul>
			</li><li class="relative">
				<a href="javascript:;" data-toggle-class data-target=".navbar-search" class="btn nav-search-btn">' . zib_svg('search') . '</a>
			</li>
		</ul>
	</div>';

	if ($layout == 2) {
		$_a = '<li><a href="javascript:;" class="signin-loader">登录</a></li><li><a href="javascript:;" class="signup-loader">注册</a></li>';
		if ($user_id) {
			$avatar = zib_get_data_avatar($user_id);
			$_a = '<li><a href="javascript:;" class="navbar-avatar">' . $avatar . '</a>
					<ul class="sub-menu">' . $sub . '</ul></li>';
		}
		$b_b = '
		<div class="navbar-right' . ($user_id ? '' : ' navbar-text') . '">
			<ul class="list-inline splitters relative">
			' . $_a . '
			</ul>
		</div>
		';
	}

	if ($layout == 3) {
		$html = $b_b . $button;
	} else {
		$html = $button . $b_b;
	}

	echo $html;
}
function zib_header_user_box()
{
	$user_id = get_current_user_id();

	$con = '';
	if ($user_id) {

		$avatar = zib_get_data_avatar($user_id);
		//$cover = '<img class="lazyload fit-cover" data-src="' . get_user_cover_img($user_id) . '">';
		$user_data = get_userdata($user_id);
		$name = $user_data->display_name;
		//$desc = get_user_desc($user_id);
		$like_n = get_user_posts_meta_count($user_id, 'like');
		$view_n = get_user_posts_meta_count($user_id, 'views');
		$followed_n = get_user_meta($user_id, 'followed-user-count', true);
		$com_n = get_user_comment_count($user_id);
		$post_n = (int) count_user_posts($user_id, 'post', true);

		$payvip = zib_get_header_payvip_icon($user_id);

		$items = '<item><span class="badg c-blue" data-toggle="tooltip" title="发布' . $post_n . '篇文章">' . zib_svg('post') . ($post_n ? $post_n : '0') . '</span></item>';
		$items .= '<item><span class="badg c-green" data-toggle="tooltip" title="发布' . $com_n . '条评论">' . zib_svg('comment') . ($com_n ? $com_n : '0') . '</span></item>';
		$items .= '<item><span class="badg c-red" data-toggle="tooltip" title="人气值 ' . $view_n . '">' . zib_svg('huo') . $view_n . '</span></item>';
		$items .= $like_n ? '<item><span class="badg c-purple" data-toggle="tooltip" title="获得' . $like_n . '个点赞">' . zib_svg('like') . $like_n . '</span></item>' : '';
		$items .= $followed_n ? '<item><span class="badg c-yellow" data-toggle="tooltip" title="共' . $followed_n . '个粉丝"><i class="fa fa-heart em09"></i>' . $followed_n . '</span></item>' : '';

		$href = '<a href="' . get_author_posts_url($user_id) . '" ><div class="badg mb6 toggle-radius c-blue">' . zib_svg('user', '50 0 924 924') . '</div><div class="c-blue">个人中心</div></a>';
		$href .= zib_get_write_posts_button('', '发布文章', '<div class="badg mb6 toggle-radius c-green"><i class="fa fa-fw fa-pencil-square-o"></i></div><div class="c-green">', '</div>');
		$href .= '<a href="javascript:;" data-toggle="modal" data-target="#modal_signout" ><div class="badg mb6 toggle-radius c-red">' . zib_svg('signout') . '</div><div class="c-red">退出登录</div></a>';
		if (is_super_admin()) {
			$href .= '</br>';
			$href .= '<a target="_blank"  href="' . of_get_menuurl() . '" ><div class="badg mb6 toggle-radius c-yellow">' . zib_svg('theme') . '</div><div class="c-yellow">主题设置</div></a>';
			$href .= '<a target="_blank"  href="' . zib_get_customize_widgets_url() . '" ><div class="badg mb6 toggle-radius c-yellow"><i class="fa fa-pie-chart"></i></div><div class="c-yellow">模块配置</div></a>';
			$href .= '<a target="_blank"  href="' . admin_url() . '" ><div class="badg mb6 toggle-radius c-yellow">' . zib_svg('set') . '</div><div class="c-yellow">后台管理</div></a>';
		}


		$con .=  '<ul class="list-inline mb10">';
		$con .=  '<li><div class="avatar-img">' . $avatar . '</div></li>';
		$con .=  '<li>';
		$con .=  '<div><b>' . $name . '</b></div>';
		$con .=  '<div>' . $payvip . '</div>';
		$con .=  '</li>';
		$con .=  '</ul>';

		$con .=  '<div class="em09 text-center author-tag mb10">' . $items . '</div>';
		$con .=  '<div class="relative"><i class="line-form-line"></i> </div>';
		$con .=  '<div class="text-center mt10 mb10 header-user-href">' . $href . '</div>';
	} else {

		$href = '<a href="javascript:;" class="signin-loader"><div class="badg mb6 toggle-radius c-blue">' . zib_svg('user', '50 0 924 924') . '</div><div class="c-blue">登录</div></a>';
		$href .= '<a href="javascript:;" class="signup-loader"><div class="badg mb6 toggle-radius c-green">' . zib_svg('signup') . '</div><div class="c-green">注册</div></a>';
		$href .= '<a target="_blank" href="' . zib_get_permalink(_pz('user_rp')) . '"><div class="badg mb6 toggle-radius c-purple">' . zib_svg('user_rp') . '</div><div class="c-purple">找回密码</div></a>';

		$con .=  '<div class="text-center mb10 header-user-href">' . $href . '</div>';
		$ocial_login = zib_social_login(false);
		if ($ocial_login) {
			$con .= '<p class="social-separator separator muted-3-color em09">快速登录</p>';
			$con .= '<div class="social_loginbar">';
			$con .= $ocial_login;
			$con .= '</div>';
		}
	}

	$html = '<div class="sub-user-box theme-box">' . $con . '</div>';
	return $html;
}
function zib_get_header_payvip_icon($user_id = 0)
{
	if (!$user_id || !_pz('pay_user_vip_1_s', true) || !_pz('pay_user_vip_2_s', true)) return;
	$vip_level = zib_get_user_vip_level($user_id);

	if ($vip_level) {
		return zibpay_get_vip_icon($vip_level) . '<span class="ml10 badg jb-yellow vip-expdate-tag" data-toggle="tooltip" title="会员有效期：' . zib_get_user_vip_exp_date_text($user_id) . '">' . zib_get_user_vip_exp_date_text($user_id) . '</span>';
	} else {
		$button = '<a class="pay-vip but jb-red radius payvip-icon" href="javascript:;">' . zib_svg('vip_1', '0 0 1024 1024', 'em12 mr3') . '开通会员</a>';
		return $button;
	}
}



function zib_header_search()
{
	$more_cats = array();
	$more_cats_obj = _pz('header_search_more_cat_obj');
	if (empty($more_cats_obj['all']) && $more_cats_obj) {
		foreach ($more_cats_obj as $key => $value) {
			if ($value) $more_cats[] = $key;
		}
	}

	$args = array(
		'class' => '',
		'show_keywords' => _pz('header_search_popular_key', true),
		'show_input_cat' => _pz('header_search_cat', true),
		'show_more_cat' => _pz('header_search_more_cat', true),
		'in_cat' => _pz('header_search_cat_in'),
		'more_cats' => $more_cats,
	);
	echo '<div class="fixed-body main-bg box-body navbar-search">';
	echo '<div class="theme-box"><button class="close" data-toggle-class data-target=".navbar-search" ><i data-svg="close" data-class="ic-close" data-viewbox="0 0 1024 1024"></i></button></div>';
	echo '<div class="box-body">';
	zib_get_search($args);
	echo '</div>';
	echo '</div>';
}


function zib_get_menu_search()
{
	$html = '
      <form method="get" class="navbar-form navbar-left" action="' . esc_url(home_url('/')) . '">
        <div class="form-group relative">
          	<input type="text" class="form-control search-input" name="s" placeholder="搜索内容">
			   <div class="abs-right muted-3-color"><button type="submit" tabindex="3" class="null">' . zib_svg('search') . '</button></div>
		</div>
      </form>';
	return $html;
}

function zib_menu_items($location = 'topmenu')
{
	$args = array(
		'container'       => false,
		'container_class' => 'nav navbar-nav',
		'echo'            => false,
		'fallback_cb'     => false,
		'items_wrap'      => '<ul class="nav navbar-nav">%3$s</ul>',
		'theme_location'  => $location,
	);
	if (!wp_is_mobile()) {
		$args['depth'] = 0;
	}
	$menu = wp_nav_menu($args);
	if (!$menu && is_super_admin()) {
		$menu = '<ul class="nav navbar-nav"><li><a href="' . admin_url('nav-menus.php') . '" class="signin-loader loaderbt">添加导航菜单</a></li></ul>';
	}
	echo $menu;
}


function zib_navbar_header()
{
	$m_layout = _pz('mobile_header_layout', 'center');

	$t = _pz('hometitle') ? _pz('hometitle') : get_bloginfo('name') . (get_bloginfo('description') ? _get_delimiter() . get_bloginfo('description') : '');
	$logo = '<a class="navbar-logo" href="' . get_bloginfo('url') . '" title="' . $t . '">'
		. zib_get_adaptive_theme_img(_pz('logo_src'), _pz('logo_src_dark'), $t, 'height="50"') . '
			</a>';
	$button = '<button type="button" data-toggle-class data-target=".mobile-navbar" class="navbar-toggle">' . zib_svg('menu', '0 0 1024 1024', 'icon em12') . '</button>';
	if ($m_layout == 'center') {
		$button .= '<button type="button" data-toggle-class data-target=".navbar-search" class="navbar-toggle">' . zib_svg('search') . '</button>';
	}

	echo '<div class="navbar-header">
			<div class="navbar-brand">' . $logo . '</div>
			' . $button . '
		</div>';
}

function zib_nav_mobile($location = 'mobilemenu')
{
	$menu = '';
	$args = array(
		'container'       => false,
		'echo'            => false,
		'fallback_cb'     => false,
		'depth'           => 3,
		'items_wrap'      => '<ul class="mobile-menus theme-box">%3$s</ul>',
		'theme_location'  => $location,
	);

	$m_layout = _pz('mobile_header_layout', 'center');

	$menu .= _pz('theme_mode_button', true) ? '<a href="javascript:;" class="toggle-theme toggle-radius">' . zib_svg('theme') . '</a>' : '';

	if ($m_layout != 'center') {
		$menu .= '<a href="javascript:;" data-toggle-class data-target=".navbar-search" class="toggle-radius">' . zib_svg('search') . '</a>';
	}

	$menu .= wp_nav_menu($args);
	$menu .= '<div class="posts-nav-box" data-title="文章目录"></div>';
	if (!wp_nav_menu($args)) {
		$args['theme_location'] = 'topmenu';
		if (wp_nav_menu($args)) {
			$menu .= wp_nav_menu($args);
		} else {
			$menu .= '<ul class="mobile-menus theme-box"><li><a href="' . admin_url('nav-menus.php') . '" class="signin-loader loaderbt">添加导航菜单</a></li></ul>';
		}
	}

	$sub = zib_header_user_box();

	echo $menu . $sub;
}
