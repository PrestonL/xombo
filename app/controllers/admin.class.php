<?php
use XOMBO\site_post as site_post;
use XOMBO\li as li;
use XOMBO\ul_li as ul_li;
use XOMBO\component as component;
use XOMBO\section_component as section_component;
use XOMBO\post_section as post_section;
class admin extends XOMBO\controller {
	public static function login () {
		try {
			session_start ();
			$_SESSION['admin'] = 1;
		} catch (exception $e) {
			// ignore
		}
		static::redirect ("/");
	}
	public static function logout () {
		try {
			session_start ();
			$_SESSION['admin'] = 0;
		} catch (exception $e) {
			// ignore
		}
		static::redirect ("/");
	}
	public static function addpost () {
		$site_post = $site->createPost ();
		static::redirect ("/site/post/" . $site_post->ID);
	}
	public static function deletepost ($site_post_ID) {
		$site_post = new site_post ($site_post_ID);
		if ($site_post->ID > 0) {
			$post = new post ($site_post->post_ID);
			if (count ($post->sections)) {
				throw new exception ("Post must be empty.");
			}
			$site_post->delete ();
			$post->delete ();
			static::redirect ("/");
		}
		throw new exception ("Could not find site_post");
	}
	public static function addsection ($site_post_ID) {
		$site_post = new site_post ($site_post_ID);
		if ($site_post->ID > 0) {
			$post = new post ($site_post->post_ID);
			$post_section = $post->createSection ();
			static::redirect ("/site/post/" . $site_post_ID);
		}
		throw new exception ("Invalid site post.");
	}
	public static function addheading ($site_post_ID, $section_ID) {
		$section = new section ($section_ID);
		if ($section->ID > 0) {
			$section_component = $section->createHeading ();
			static::redirect ("/site/post/" . $site_post_ID);
		}
		throw new exception ("Invalid section.");
	}
	public static function addp ($site_post_ID, $section_ID) {
		$section = new section ($section_ID);
		if ($section->ID > 0) {
			$section_component = $section->createP ();
			static::redirect ("/site/post/" . $site_post_ID);
		}
		throw new exception ("Invalid section.");
	}
	public static function addaside ($site_post_ID, $section_ID) {
		$section = new section ($section_ID);
		if ($section->ID > 0) {
			$section_component = $section->createAside ();
			static::redirect ("/site/post/" . $site_post_ID);
		}
		throw new exception ("Invalid section.");	
	}
	public static function addfigure ($site_post_ID, $section_ID) {
		$section = new section ($section_ID);
		if ($section->ID > 0) {
			$section_component = $section->createFigure ();
			static::redirect ("/site/post/" . $site_post_ID);
		}
		throw new exception ("Invalid section.");	
	}
	public static function addnav ($site_post_ID, $section_ID) {
		$section = new section ($section_ID);
		if ($section->ID > 0) {
			$section_component = $section->createNav ();
			static::redirect ("/site/post/" . $site_post_ID);
		}
		throw new exception ("Invalid section.");	
	}
	public static function addul ($site_post_ID, $section_ID) {
		$section = new section ($section_ID);
		if ($section->ID > 0) {
			$section_component = $section->createUl ();
			static::redirect ("/site/post/" . $site_post_ID);
		}
		throw new exception ("Invalid section.");	
	}
	public static function addol ($site_post_ID, $section_ID) {
		$section = new section ($section_ID);
		if ($section->ID > 0) {
			$section_component = $section->createOl ();
			static::redirect ("/site/post/" . $site_post_ID);
		}
		throw new exception ("Invalid section.");	
	}
	public static function addli ($site_post_ID, $ul_ID) {
		$ul = new ul ($ul_ID);
		if ($ul->ID > 0) {
			$li = $ul->createLi ();
			static::redirect ("/site/post/" . $site_post_ID);
		}
		throw new exception ("Invalid section.");
	}
	public static function upli ($site_post_ID, $ul_li_ID) {
		$ul_li = new ul_li ($ul_li_ID);
		if ($ul_li->ID > 0) {
			$query = "SELECT `ID` FROM `ul_li` WHERE `ul_ID`='" . $ul_li->ul_ID . "' AND `order`<'" . $ul_li->order . "' ORDER BY `order` DESC LIMIT 1";
			$result = XOMBO\DB::query ($query);
			while ($row = $result->fetch_assoc ()) {
				$ul_li_above = new ul_li ($row['ID']);
				$order = $ul_li->order;
				$ul_li->order = $ul_li_above->order;
				$ul_li->save ();
				$ul_li_above->order = $order;
				$ul_li_above->save ();
			}
			static::redirect ("/site/post/" . $site_post_ID);
		}
		throw new exception ("Invalid ul_li.");
	}
	public static function downli ($site_post_ID, $ul_li_ID) {
		$ul_li = new ul_li ($ul_li_ID);
		if ($ul_li->ID > 0) {
			$query = "SELECT `ID` FROM `ul_li` WHERE `ul_ID`='" . $ul_li->ul_ID . "' AND `order`>'" . $ul_li->order . "' ORDER BY `order` ASC LIMIT 1";
			$result = XOMBO\DB::query ($query);
			while ($row = $result->fetch_assoc ()) {
				$ul_li_below = new ul_li ($row['ID']);
				$order = $ul_li->order;
				$ul_li->order = $ul_li_below->order;
				$ul_li->save ();
				$ul_li_below->order = $order;
				$ul_li_below->save ();
			}
			static::redirect ("/site/post/" . $site_post_ID);
		}
		throw new exception ("Invalid ul_li.");
	}
	public static function deleteli ($site_post_ID, $ul_li_ID) {
		$ul_li = new ul_li ($ul_li_ID);
		if ($ul_li->ID > 0) {
			$li = new li ($ul_li->li_ID);
			$li->delete ();
			$ul_ID = $ul_li->ul_ID;
			$ul_li->delete ();
			ul_li::select (array ('ul_ID' => $ul_ID), array ('order' => 'ASC'))->bind (
				function ($obj) {
					static $i = 0;
					$i++;
					$obj->order = $i;
					$obj->save ();
					return $obj;
				}
			)->getArray ();
			static::redirect ("/site/post/" . $site_post_ID);
		}
		throw new exception ("Invalid ul_li.");
	}
	public static function strikeli ($site_post_ID, $li_ID) {
		$li = new li ($li_ID);
		$li->strikeout = $li->strikeout ? 0 : 1;
		$li->save ();
		static::redirect ("/site/post/" . $site_post_ID);	
	}
	public static function summaryp ($site_post_ID, $p_ID) {
		$p = new p ($p_ID);
		$p->summary = $p->summary ? 0 : 1;
		$p->save ();
		static::redirect ("/site/post/" . $site_post_ID);
	}
	public static function upcomponent ($site_post_ID, $section_component_ID) {
		$section_component = new section_component ($section_component_ID);
		if ($section_component->ID > 0) {
			$query = "SELECT `ID` FROM `section_component` WHERE `section_ID`='" . $section_component->section_ID . "' AND `order`<'" . $section_component->order . "' ORDER BY `order` DESC LIMIT 1";
			$result = XOMBO\DB::query ($query);
			while ($row = $result->fetch_assoc ()) {
				$section_component_above = new section_component ($row['ID']);
				$order = $section_component->order;
				$section_component->order = $section_component_above->order;
				$section_component->save ();
				$section_component_above->order = $order;
				$section_component_above->save ();
			}
			static::redirect ("/site/post/" . $site_post_ID);
		}
		throw new exception ("Invalid section component.");
	}
	public static function downcomponent ($site_post_ID, $section_component_ID) {
		$section_component = new section_component ($section_component_ID);
		if ($section_component->ID > 0) {
			$query = "SELECT `ID` FROM `section_component` WHERE `section_ID`='" . $section_component->section_ID . "' AND `order`>'" . $section_component->order . "' ORDER BY `order` ASC LIMIT 1";
			$result = XOMBO\DB::query ($query);
			while ($row = $result->fetch_assoc ()) {
				$section_component_below = new section_component ($row['ID']);
				$order = $section_component->order;
				$section_component->order = $section_component_below->order;
				$section_component->save ();
				$section_component_below->order = $order;
				$section_component_below->save ();
			}
			static::redirect ("/site/post/" . $site_post_ID);
		}
		throw new exception ("Invalid section component.");
	}
	public static function deletecomponent ($site_post_ID, $section_component_ID) {
		$section_component = new section_component ($section_component_ID);
		if ($section_component->ID > 0) {
			$component = new component ($section_component->component_ID);
			$actual_component = new $component->type ($component->ref_ID);
			$actual_component->delete ();
			if ($component->type == 'ul' || $component->type == 'ol') {
				ul_li::select (array ('ul_ID' => $component->ref_ID))->bind (
					function ($obj) {
						$li = new li ($obj->li_ID);
						$li->delete ();
						$obj->delete ();
						return;
					}
				)->getArray ();
			}
			$component->delete ();
			$section_component->delete ();
			section_component::select (array ('section_ID' => $section_component->section_ID), array ('order' => 'ASC'))->bind (
				function ($obj) {
					static $i = 0;
					$i++;
					$obj->order = $i;
					$obj->save ();
					return $obj;
				}
			)->getArray ();
			static::redirect ("/site/post/" . $site_post_ID);
		}
		throw new exception ("Invalid section component.");
	}
	public static function upsection ($site_post_ID, $post_section_ID) {
		$post_section = new post_section ($post_section_ID);
		if ($post_section->ID > 0) {
			$query = "SELECT `ID` FROM `post_section` WHERE `post_ID`='" . $post_section->post_ID . "' AND `order`<'" . $post_section->order . "' ORDER BY `order` DESC LIMIT 1";
			$result = XOMBO\DB::query ($query);
			while ($row = $result->fetch_assoc ()) {
				$post_section_above = new post_section ($row['ID']);
				$order = $post_section->order;
				$post_section->order = $post_section_above->order;
				$post_section->save ();
				$post_section_above->order = $order;
				$post_section_above->save ();
			}
			static::redirect ("/site/post/" . $site_post_ID);
		}
		throw new exception ("Invalid post section.");
	}
	public static function downsection ($site_post_ID, $post_section_ID) {
		$post_section = new post_section ($post_section_ID);
		if ($post_section->ID > 0) {
			$query = "SELECT `ID` FROM `post_section` WHERE `post_ID`='" . $post_section->post_ID . "' AND `order`>'" . $post_section->order . "' ORDER BY `order` ASC LIMIT 1";
			$result = XOMBO\DB::query ($query);
			while ($row = $result->fetch_assoc ()) {
				$post_section_below = new post_section ($row['ID']);
				$order = $post_section->order;
				$post_section->order = $post_section_below->order;
				$post_section->save ();
				$post_section_below->order = $order;
				$post_section_below->save ();
			}
			static::redirect ("/site/post/" . $site_post_ID);
		}
		throw new exception ("Invalid post section.");
	}
	public static function deletesection ($site_post_ID, $post_section_ID) {
		$post_section = new post_section ($post_section_ID);
		if ($post_section->ID > 0) {
			$section = new section ($post_section->section_ID);
			if (count ($section->components)) {
				throw new exception ("section must be empty");
			}
			$post_ID = $post_section->post_ID;
			$section->delete ();
			$post_section->delete ();
			post_section::select (array ('post_ID' => $post_ID), array ('order' => 'ASC'))->bind (
				function ($obj) {
					static $i = 0;
					$i++;
					$obj->order = $i;
					$obj->save ();
					return $obj;
				}
			)->getArray ();
			static::redirect ("/site/post/" . $site_post_ID);
		}
		throw new exception ("Invalid post section.");
	}
	public static function redirect ($url) {
		ob_end_clean ();
		header ("Location: " . $url);
		die ();
	}
}
