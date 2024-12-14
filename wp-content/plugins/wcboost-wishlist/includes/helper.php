<?php
namespace WCBoost\Wishlist;

defined( 'ABSPATH' ) || exit;

class Helper {

	/**
	 * Get wishlist session id of guests.
	 *
	 * @since 1.1.2 Moved to class \WCBoost\Wishlist\Session
	 *
	 * @return string
	 */
	public static function get_session_id() {
		return Session::get_session_id();
	}

	/**
	 * Set session id for guests.
	 * Store the session id in cookie for 30 days. It can be changed via a hook.
	 *
	 * @since 1.1.2 Moved to class \WCBoost\Wishlist\Session
	 *
	 * @param string $session_id
	 */
	public static function set_session_id( $session_id ) {
		Session::set_session_id( $session_id );
	}

	/**
	 * Clear session id in the cookie
	 *
	 * @since 1.1.2 Moved to class \WCBoost\Wishlist\Session
	 */
	public static function clear_session_id() {
		Session::clear_session_id();
	}

	/**
	 * Determines whether the query is for the wishlist page
	 *
	 * @return bool
	 */
	public static function is_wishlist() {
		$page_id = wc_get_page_id( 'wishlist' );

		if ( ! $page_id ) {
			return false;
		}

		return is_page( $page_id );
	}

	/**
	 * Get svg icon for the add to wishlist button.
	 *
	 * @param string $type
	 * @return string
	 */
	public static function get_icon( $icon = 'add', $size = 24 ) {
		$svg = '';

		switch ( $icon ) {
			case 'spinner':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M304 48C304 74.51 282.5 96 256 96C229.5 96 208 74.51 208 48C208 21.49 229.5 0 256 0C282.5 0 304 21.49 304 48zM304 464C304 490.5 282.5 512 256 512C229.5 512 208 490.5 208 464C208 437.5 229.5 416 256 416C282.5 416 304 437.5 304 464zM0 256C0 229.5 21.49 208 48 208C74.51 208 96 229.5 96 256C96 282.5 74.51 304 48 304C21.49 304 0 282.5 0 256zM512 256C512 282.5 490.5 304 464 304C437.5 304 416 282.5 416 256C416 229.5 437.5 208 464 208C490.5 208 512 229.5 512 256zM74.98 437C56.23 418.3 56.23 387.9 74.98 369.1C93.73 350.4 124.1 350.4 142.9 369.1C161.6 387.9 161.6 418.3 142.9 437C124.1 455.8 93.73 455.8 74.98 437V437zM142.9 142.9C124.1 161.6 93.73 161.6 74.98 142.9C56.24 124.1 56.24 93.73 74.98 74.98C93.73 56.23 124.1 56.23 142.9 74.98C161.6 93.73 161.6 124.1 142.9 142.9zM369.1 369.1C387.9 350.4 418.3 350.4 437 369.1C455.8 387.9 455.8 418.3 437 437C418.3 455.8 387.9 455.8 369.1 437C350.4 418.3 350.4 387.9 369.1 369.1V369.1z"/></svg>';
				break;

			case 'heart':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M244 84L255.1 96L267.1 84.02C300.6 51.37 347 36.51 392.6 44.1C461.5 55.58 512 115.2 512 185.1V190.9C512 232.4 494.8 272.1 464.4 300.4L283.7 469.1C276.2 476.1 266.3 480 256 480C245.7 480 235.8 476.1 228.3 469.1L47.59 300.4C17.23 272.1 0 232.4 0 190.9V185.1C0 115.2 50.52 55.58 119.4 44.1C164.1 36.51 211.4 51.37 244 84C243.1 84 244 84.01 244 84L244 84zM255.1 163.9L210.1 117.1C188.4 96.28 157.6 86.4 127.3 91.44C81.55 99.07 48 138.7 48 185.1V190.9C48 219.1 59.71 246.1 80.34 265.3L256 429.3L431.7 265.3C452.3 246.1 464 219.1 464 190.9V185.1C464 138.7 430.4 99.07 384.7 91.44C354.4 86.4 323.6 96.28 301.9 117.1L255.1 163.9z"/></svg>';
				break;

			case 'heart-filled':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M0 190.9V185.1C0 115.2 50.52 55.58 119.4 44.1C164.1 36.51 211.4 51.37 244 84.02L256 96L267.1 84.02C300.6 51.37 347 36.51 392.6 44.1C461.5 55.58 512 115.2 512 185.1V190.9C512 232.4 494.8 272.1 464.4 300.4L283.7 469.1C276.2 476.1 266.3 480 256 480C245.7 480 235.8 476.1 228.3 469.1L47.59 300.4C17.23 272.1 .0003 232.4 .0003 190.9L0 190.9z"/></svg>';
				break;

			case 'star':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M287.9 0C297.1 0 305.5 5.25 309.5 13.52L378.1 154.8L531.4 177.5C540.4 178.8 547.8 185.1 550.7 193.7C553.5 202.4 551.2 211.9 544.8 218.2L433.6 328.4L459.9 483.9C461.4 492.9 457.7 502.1 450.2 507.4C442.8 512.7 432.1 513.4 424.9 509.1L287.9 435.9L150.1 509.1C142.9 513.4 133.1 512.7 125.6 507.4C118.2 502.1 114.5 492.9 115.1 483.9L142.2 328.4L31.11 218.2C24.65 211.9 22.36 202.4 25.2 193.7C28.03 185.1 35.5 178.8 44.49 177.5L197.7 154.8L266.3 13.52C270.4 5.249 278.7 0 287.9 0L287.9 0zM287.9 78.95L235.4 187.2C231.9 194.3 225.1 199.3 217.3 200.5L98.98 217.9L184.9 303C190.4 308.5 192.9 316.4 191.6 324.1L171.4 443.7L276.6 387.5C283.7 383.7 292.2 383.7 299.2 387.5L404.4 443.7L384.2 324.1C382.9 316.4 385.5 308.5 391 303L476.9 217.9L358.6 200.5C350.7 199.3 343.9 194.3 340.5 187.2L287.9 78.95z"/></svg>';
				break;

			case 'star-filled':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M381.2 150.3L524.9 171.5C536.8 173.2 546.8 181.6 550.6 193.1C554.4 204.7 551.3 217.3 542.7 225.9L438.5 328.1L463.1 474.7C465.1 486.7 460.2 498.9 450.2 506C440.3 513.1 427.2 514 416.5 508.3L288.1 439.8L159.8 508.3C149 514 135.9 513.1 126 506C116.1 498.9 111.1 486.7 113.2 474.7L137.8 328.1L33.58 225.9C24.97 217.3 21.91 204.7 25.69 193.1C29.46 181.6 39.43 173.2 51.42 171.5L195 150.3L259.4 17.97C264.7 6.954 275.9-.0391 288.1-.0391C300.4-.0391 311.6 6.954 316.9 17.97L381.2 150.3z"/></svg>';
				break;

			case 'bookmark':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M336 0h-288C21.49 0 0 21.49 0 48v431.9c0 24.7 26.79 40.08 48.12 27.64L192 423.6l143.9 83.93C357.2 519.1 384 504.6 384 479.9V48C384 21.49 362.5 0 336 0zM336 452L192 368l-144 84V54C48 50.63 50.63 48 53.1 48h276C333.4 48 336 50.63 336 54V452z"/></svg>';
				break;

			case 'bookmark-filled':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M384 48V512l-192-112L0 512V48C0 21.5 21.5 0 48 0h288C362.5 0 384 21.5 384 48z"/></svg>';
				break;

			case 'facebook':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"/></svg>';
				break;

			case 'twitter':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/></svg>';
				break;

			case 'email':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M464 64C490.5 64 512 85.49 512 112C512 127.1 504.9 141.3 492.8 150.4L275.2 313.6C263.8 322.1 248.2 322.1 236.8 313.6L19.2 150.4C7.113 141.3 0 127.1 0 112C0 85.49 21.49 64 48 64H464zM217.6 339.2C240.4 356.3 271.6 356.3 294.4 339.2L512 176V384C512 419.3 483.3 448 448 448H64C28.65 448 0 419.3 0 384V176L217.6 339.2z"/></svg>';
				break;

			case 'linkedin':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"/></svg>';
				break;

			case 'tumblr':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M309.8 480.3c-13.6 14.5-50 31.7-97.4 31.7-120.8 0-147-88.8-147-140.6v-144H17.9c-5.5 0-10-4.5-10-10v-68c0-7.2 4.5-13.6 11.3-16 62-21.8 81.5-76 84.3-117.1.8-11 6.5-16.3 16.1-16.3h70.9c5.5 0 10 4.5 10 10v115.2h83c5.5 0 10 4.4 10 9.9v81.7c0 5.5-4.5 10-10 10h-83.4V360c0 34.2 23.7 53.6 68 35.8 4.8-1.9 9-3.2 12.7-2.2 3.5.9 5.8 3.4 7.4 7.9l22 64.3c1.8 5 3.3 10.6-.4 14.5z"/></svg>';
				break;

			case 'reddit':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M201.5 305.5c-13.8 0-24.9-11.1-24.9-24.6 0-13.8 11.1-24.9 24.9-24.9 13.6 0 24.6 11.1 24.6 24.9 0 13.6-11.1 24.6-24.6 24.6zM504 256c0 137-111 248-248 248S8 393 8 256 119 8 256 8s248 111 248 248zm-132.3-41.2c-9.4 0-17.7 3.9-23.8 10-22.4-15.5-52.6-25.5-86.1-26.6l17.4-78.3 55.4 12.5c0 13.6 11.1 24.6 24.6 24.6 13.8 0 24.9-11.3 24.9-24.9s-11.1-24.9-24.9-24.9c-9.7 0-18 5.8-22.1 13.8l-61.2-13.6c-3-.8-6.1 1.4-6.9 4.4l-19.1 86.4c-33.2 1.4-63.1 11.3-85.5 26.8-6.1-6.4-14.7-10.2-24.1-10.2-34.9 0-46.3 46.9-14.4 62.8-1.1 5-1.7 10.2-1.7 15.5 0 52.6 59.2 95.2 132 95.2 73.1 0 132.3-42.6 132.3-95.2 0-5.3-.6-10.8-1.9-15.8 31.3-16 19.8-62.5-14.9-62.5zM302.8 331c-18.2 18.2-76.1 17.9-93.6 0-2.2-2.2-6.1-2.2-8.3 0-2.5 2.5-2.5 6.4 0 8.6 22.8 22.8 87.3 22.8 110.2 0 2.5-2.2 2.5-6.1 0-8.6-2.2-2.2-6.1-2.2-8.3 0zm7.7-75c-13.6 0-24.6 11.1-24.6 24.9 0 13.6 11.1 24.6 24.6 24.6 13.8 0 24.9-11.1 24.9-24.6 0-13.8-11-24.9-24.9-24.9z"/></svg>';
				break;

			case 'stumbleupon':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M502.9 266v69.7c0 62.1-50.3 112.4-112.4 112.4-61.8 0-112.4-49.8-112.4-111.3v-70.2l34.3 16 51.1-15.2V338c0 14.7 12 26.5 26.7 26.5S417 352.7 417 338v-72h85.9zm-224.7-58.2l34.3 16 51.1-15.2V173c0-60.5-51.1-109-112.1-109-60.8 0-112.1 48.2-112.1 108.2v162.4c0 14.9-12 26.7-26.7 26.7S86 349.5 86 334.6V266H0v69.7C0 397.7 50.3 448 112.4 448c61.6 0 112.4-49.5 112.4-110.8V176.9c0-14.7 12-26.7 26.7-26.7s26.7 12 26.7 26.7v30.9z"/></svg>';
				break;

			case 'telegram':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 496 512"><path d="M248,8C111.033,8,0,119.033,0,256S111.033,504,248,504,496,392.967,496,256,384.967,8,248,8ZM362.952,176.66c-3.732,39.215-19.881,134.378-28.1,178.3-3.476,18.584-10.322,24.816-16.948,25.425-14.4,1.326-25.338-9.517-39.287-18.661-21.827-14.308-34.158-23.215-55.346-37.177-24.485-16.135-8.612-25,5.342-39.5,3.652-3.793,67.107-61.51,68.335-66.746.153-.655.3-3.1-1.154-4.384s-3.59-.849-5.135-.5q-3.283.746-104.608,69.142-14.845,10.194-26.894,9.934c-8.855-.191-25.888-5.006-38.551-9.123-15.531-5.048-27.875-7.717-26.8-16.291q.84-6.7,18.45-13.7,108.446-47.248,144.628-62.3c68.872-28.647,83.183-33.623,92.511-33.789,2.052-.034,6.639.474,9.61,2.885a10.452,10.452,0,0,1,3.53,6.716A43.765,43.765,0,0,1,362.952,176.66Z"/></svg>';
				break;

			case 'whatsapp':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>';
				break;

			case 'pocket':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M407.6 64h-367C18.5 64 0 82.5 0 104.6v135.2C0 364.5 99.7 464 224.2 464c124 0 223.8-99.5 223.8-224.2V104.6c0-22.4-17.7-40.6-40.4-40.6zm-162 268.5c-12.4 11.8-31.4 11.1-42.4 0C89.5 223.6 88.3 227.4 88.3 209.3c0-16.9 13.8-30.7 30.7-30.7 17 0 16.1 3.8 105.2 89.3 90.6-86.9 88.6-89.3 105.5-89.3 16.9 0 30.7 13.8 30.7 30.7 0 17.8-2.9 15.7-114.8 123.2z"/></svg>';
				break;

			case 'digg':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M81.7 172.3H0v174.4h132.7V96h-51v76.3zm0 133.4H50.9v-92.3h30.8v92.3zm297.2-133.4v174.4h81.8v28.5h-81.8V416H512V172.3H378.9zm81.8 133.4h-30.8v-92.3h30.8v92.3zm-235.6 41h82.1v28.5h-82.1V416h133.3V172.3H225.1v174.4zm51.2-133.3h30.8v92.3h-30.8v-92.3zM153.3 96h51.3v51h-51.3V96zm0 76.3h51.3v174.4h-51.3V172.3z"/></svg>';
				break;

			case 'vk':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M31.4907 63.4907C0 94.9813 0 145.671 0 247.04V264.96C0 366.329 0 417.019 31.4907 448.509C62.9813 480 113.671 480 215.04 480H232.96C334.329 480 385.019 480 416.509 448.509C448 417.019 448 366.329 448 264.96V247.04C448 145.671 448 94.9813 416.509 63.4907C385.019 32 334.329 32 232.96 32H215.04C113.671 32 62.9813 32 31.4907 63.4907ZM75.6 168.267H126.747C128.427 253.76 166.133 289.973 196 297.44V168.267H244.16V242C273.653 238.827 304.64 205.227 315.093 168.267H363.253C359.313 187.435 351.46 205.583 340.186 221.579C328.913 237.574 314.461 251.071 297.733 261.227C316.41 270.499 332.907 283.63 346.132 299.751C359.357 315.873 369.01 334.618 374.453 354.747H321.44C316.555 337.262 306.614 321.61 292.865 309.754C279.117 297.899 262.173 290.368 244.16 288.107V354.747H238.373C136.267 354.747 78.0267 284.747 75.6 168.267Z"/></svg>';
				break;

			case 'link':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path d="M579.8 267.7c56.5-56.5 56.5-148 0-204.5c-50-50-128.8-56.5-186.3-15.4l-1.6 1.1c-14.4 10.3-17.7 30.3-7.4 44.6s30.3 17.7 44.6 7.4l1.6-1.1c32.1-22.9 76-19.3 103.8 8.6c31.5 31.5 31.5 82.5 0 114L422.3 334.8c-31.5 31.5-82.5 31.5-114 0c-27.9-27.9-31.5-71.8-8.6-103.8l1.1-1.6c10.3-14.4 6.9-34.4-7.4-44.6s-34.4-6.9-44.6 7.4l-1.1 1.6C206.5 251.2 213 330 263 380c56.5 56.5 148 56.5 204.5 0L579.8 267.7zM60.2 244.3c-56.5 56.5-56.5 148 0 204.5c50 50 128.8 56.5 186.3 15.4l1.6-1.1c14.4-10.3 17.7-30.3 7.4-44.6s-30.3-17.7-44.6-7.4l-1.6 1.1c-32.1 22.9-76 19.3-103.8-8.6C74 372 74 321 105.5 289.5L217.7 177.2c31.5-31.5 82.5-31.5 114 0c27.9 27.9 31.5 71.8 8.6 103.9l-1.1 1.6c-10.3 14.4-6.9 34.4 7.4 44.6s34.4 6.9 44.6-7.4l1.1-1.6C433.5 260.8 427 182 377 132c-56.5-56.5-148-56.5-204.5 0L60.2 244.3z"/></svg>';
				break;
		}

		if ( $svg ) {
			$svg = str_replace( '<svg', '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" role="image"', $svg );
		}

		return apply_filters( 'wcboost_wishlist_svg_icon', $svg, $icon );
	}

	/**
	 * Get wishlist icon
	 *
	 * @param bool $filled
	 * @param int  $size
	 * @return string
	 */
	public static function get_wishlist_icon( $filled = false, $size = 24 ) {
		$icon = get_option( 'wcboost_wishlist_button_icon', 'heart' );

		if ( ! $icon ) {
			return '';
		}

		if ( 'custom' === $icon ) {
			$custom = get_option( 'wcboost_wishlist_button_icon_custom', [ 'default' => '', 'added' => '' ] );
			$url    = $filled ? $custom['added'] : $custom['default'];
			$svg    = $url ? '<img src="' . esc_url( $url ) . '" alt="' . esc_attr__( 'Wishlist', 'wcboost-wishlist' ) . '" />' : '';
		} else {
			$icon = $filled ? $icon . '-filled' : $icon;
			$svg  = self::get_icon( $icon, $size );

			// Ensure the SVG code is always set.
			if ( ! $svg ) {
				$icon = $filled ? 'heart-filled' : 'heart';
				$svg  = self::get_icon( $icon, $size );
			}
		}

		return apply_filters( 'wcboost_wishlist_button_icon', $svg, $icon );
	}

	/**
	 * Get button text
	 *
	 * @param string $type
	 * @return string
	 */
	public static function get_button_text( $type = 'add' ) {
		$type = in_array( $type, [ 'add', 'remove', 'view' ] ) ? $type : 'add';
		$button_text = wp_parse_args( get_option( 'wcboost_wishlist_button_text', [] ), [
			'add'    => __( 'Add to wishlist', 'wcboost-wishlist' ),
			'remove' => __( 'Remove from wishlist', 'wcboost-wishlist' ),
			'view'   => __( 'View wishlist', 'wcboost-wishlist' ),
		] );

		$text = array_key_exists( $type, $button_text ) ? $button_text[ $type ] : $button_text['add'];

		return apply_filters( 'wcboost_wishlist_button_' . $type . '_text', $text );
	}

	/**
	 * Get social sharing url
	 *
	 * @param string $social
	 * @param \WCBoost\Wishlist\Wishlist $wishlist
	 * @return string
	 */
	public static function get_share_url( $social, $wishlist = false ) {
		$wishlist     = $wishlist ? $wishlist : self::get_wishlist();
		$wishlist_url = $wishlist->get_public_url();
		$url          = '';

		if ( ! $wishlist_url ) {
			return '';
		}

		switch ( $social ) {
			case 'facebook':
				$url = add_query_arg( [ 'u' => urlencode( $wishlist_url ) ], 'https://www.facebook.com/sharer.php' );
				break;

			case 'twitter':
				$url = add_query_arg( [ 'url' => urlencode( $wishlist_url ), 'text' => $wishlist->get_wishlist_title() ], 'https://twitter.com/intent/tweet' );
				break;

			case 'linkedin':
				$url = add_query_arg( [ 'url' => urlencode( $wishlist_url ), 'title' => $wishlist->get_wishlist_title() ], 'https://www.linkedin.com/shareArticle' );
				break;

			case 'tumblr':
				$url = add_query_arg( [ 'url' => urlencode( $wishlist_url ), 'name' => $wishlist->get_wishlist_title() ], 'https://www.tumblr.com/share/link' );
				break;

			case 'reddit':
				$url = add_query_arg( [ 'url' => urlencode( $wishlist_url ), 'title' => $wishlist->get_wishlist_title() ], 'https://reddit.com/submit' );
				break;

			case 'stumbleupon':
				$url = add_query_arg( [ 'url' => urlencode( $wishlist_url ), 'title' => $wishlist->get_wishlist_title() ], 'https://www.stumbleupon.com/submit' );
				break;

			case 'telegram':
				$url = add_query_arg( [ 'url' => urlencode( $wishlist_url ) ], 'https://t.me/share/url' );
				break;

			case 'whatsapp':
				$url = add_query_arg( [ 'text' => urlencode( $wishlist_url ) ], 'https://wa.me/' );
				break;

			case 'pocket':
				$url = add_query_arg( [ 'url' => urlencode( $wishlist_url ), 'title' => $wishlist->get_wishlist_title() ], 'https://getpocket.com/save' );
				break;

			case 'digg':
				$url = add_query_arg( [ 'url' => urlencode( $wishlist_url ) ], 'https://digg.com/submit' );
				break;

			case 'vk':
				$url = add_query_arg( [ 'url' => urlencode( $wishlist_url ) ], 'https://vk.com/share.php' );
				break;

			case 'email':
				$url = sprintf( 'mailto:?subject=%s&body=%s', esc_html__( 'I have some thing to share with you', 'wcboost-wishlist' ), $wishlist_url );
				break;

			case 'link':
				$url = $wishlist_url;
				break;
		}

		return apply_filters( 'wcboost_wishlist_share_url', $url, $social, $wishlist );
	}

	/**
	 * Get social sharing link
	 *
	 * @param string $social
	 * @param \WCBoost\Wishlist\Wishlist $wishlist
	 * @return string
	 */
	public static function get_share_link( $social, $wishlist = false ) {
		$url = self::get_share_url( $social, $wishlist );

		if ( ! $url ) {
			return;
		}

		$icon = self::get_icon( $social );
		$text = 'link' == $social ? __( 'Copy link', 'wcboost-wishlist' ) : ucwords( $social );
		$link = sprintf(
			'<a href="%s" target="_blank" aria-label="%s" class="wcboost-wishlist-share-link" data-social="%s" rel="nofollow">
				<span class="wcboost-wishlist-share-link__icon">%s</span>
				<span class="wcboost-wishlist-share-link__text">%s</span>
			</a>',
			esc_url( $url ),
			/* translators: %s social name */
			esc_attr( 'link' == $social ? $text : sprintf( __( 'Share on %s', 'wcboost-wishlist' ), $text ) ),
			esc_attr( $social ),
			$icon,
			esc_html( $text )
		);

		return apply_filters( 'wcboost_wishlist_share_link', $link, $url, $text, $social, $wishlist );
	}

	/**
	 * Get the wishlist instance.
	 *
	 * @param int|string $id The wishlist ID or Token
	 *
	 * @return \WCBoost\Wishlist\Wishlist
	 */
	public static function get_wishlist( $id = false ) {
		return Plugin::instance()->query->get_wishlist( $id );
	}

	/**
	 * Render the wishlist widget content.
	 *
	 * @param array $args The arguments to render widget content
	 *
	 * @return void
	 */
	public static function widget_content( $args = [] ) {
		$args = wp_parse_args( $args, [
			'list_class'    => '',
			'show_price'    => true,
			'show_stock'    => false,
			'show_quantity' => false,
			'show_date'     => false,
			'show_purchase' => false,
			'show_buttons'  => true,
			'wishlist'      => self::get_wishlist(),
		] );

		echo '<div class="wcboost-wishlist-widget-content">';
		wc_get_template( 'wishlist/wishlist-widget.php', $args, '', Plugin::instance()->plugin_path() . '/templates/' );
		echo '</div>';
	}
}
