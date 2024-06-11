/* The following functions contain code from WP and Divi Icons Pro (fb.js) by Aspen Grove Studios */
function ds_divi_extras_onCreateElementWithClass(className, callback) {
	var MO = window.MutationObserver ? window.MutationObserver : window.WebkitMutationObserver;
	if (MO) {
		(new MO(function(events) {
			jQuery.each(events, function(i, event) {
				if (event.addedNodes && event.addedNodes.length) {
					var $nodes = jQuery(event.addedNodes);

					$nodes.filter('.' + className).each(function(i, node) {
						callback(node);
					});
					$nodes.find('.' + className).each(function(i, node) {
						callback(node);
					});
				}
			});
		})).observe(document.body, {characterData: false, childList: true, subtree: true});
	}
}

ds_divi_extras_onCreateElementWithClass('et-fb-modal__support-notice', function(node) {
	const locale = ET_Builder.Frames.app.ETBuilderBackendDynamic.locale;
	var ourModuleTitles, settingString;
	switch (locale) {
		case 'pl_PL':
			ourModuleTitles = [
				'Ogłoszenie',
				'Reklamy',
				'Suwak wyróżnionych postów',
				'Posty',
				'Blog Feed Standard',
				'Blog Feed Masonry',
				'Posty Karuzela',
				'Patka',
				'Posty z zakładkami'
			];
			settingString = 'ustawienia';
			break;

		case 'bg_BG':
			ourModuleTitles = [
				'реклама',
				'Обяви',
				'Препоръчани слайдерни постове',
				'Публикации',
				'Стандарт за подаване на блог',
				'Масонство в блога',
				'Публикации Карусел',
				'Раздел',
				'Постове с раздели'
			];
			settingString = 'Настройки за';
			break;

		case 'cs_CZ':
			ourModuleTitles = [
				'Inzerát',
				'Reklamy',
				'Doporučené příspěvky Posuvník',
				'Příspěvky',
				'Standard zdroje blogu',
				'Blog Feed Masonry',
				'Příspěvky Carousel',
				'Tab',
				'Tabbed Posts'
			];
			settingString = 'nastavení';
			break;

		case 'da_DK':
			ourModuleTitles = [
				'Ad',
				'annoncer',
				'Udvalgte indlæg Slider',
				'Indlæg',
				'Blog Feed Standard',
				'Blog Feed Murværk',
				'Stillinger Carousel',
				'Tab',
				'Tabbed Posts'
			];
			settingString = 'Indstillinger';
			break;

		case 'de_DE':
			ourModuleTitles = [
				'Anzeige',
				'Anzeigen',
				'Empfohlene Beiträge Slider',
				'Beiträge',
				'Blog Feed Standard',
				'Blog-Feed Mauerwerk',
				'Beiträge Karussell',
				'Tab',
				'Tabbed Posts'
			];
			settingString = 'Einstellungen';
			break;

		case 'el':
			ourModuleTitles = [
				'Ενα δ',
				'Διαφημίσεις',
				'Προτεινόμενα μηνύματα',
				'Δημοσιεύσεις',
				'Κανονική ροή ιστολογίου',
				'Τροφοδοσία Blog Τοιχοποιία',
				'Θέσεις καρουσέλ',
				'Αυτί',
				'Μηνύματα με καρτέλες'
			];
			settingString = 'Ρυθμίσεις';
			break;

		case 'es_ES':
			ourModuleTitles = [
				'Anuncio',
				'Anuncios',
				'Slider de publicaciones destacadas',
				'Mensajes',
				'Blog Feed Standard',
				'Blog Feed Masonería',
				'Carrusel de mensajes',
				'Lengüeta',
				'Publicaciones tabuladas'
			];
			settingString = 'Configuración';
			break;

		case 'fi':
			ourModuleTitles = [
				'Ilmoitus',
				'mainoksia',
				'Suositellut viestit Slider',
				'Viestejä',
				'Blogin syöttöstandardi',
				'Blogin syöte',
				'Posts Carousel',
				'kieleke',
				'Viestit'
			];
			settingString = 'asetukset';
			break;

		case 'fr_FR':
			ourModuleTitles = [
				'Un d',
				'Les publicités',
				'Messages en vedette Curseur',
				'Des postes',
				'Flux de blog standard',
				'Blog Feed Maçonnerie',
				'Posts carrousel',
				'Languette',
				'Poteaux à onglets'
			];
			settingString = 'Paramètres';
			break;

		case 'he_IL':
			ourModuleTitles = [
				'מודעה',
				'מודעות',
				'הודעות נבחרות Slider',
				'הודעות',
				'עדכון הזנת בלוג',
				'הבלוג להאכיל את הבונים',
				'הודעות קרוסלה',
				'לשונית',
				'"הודעות עם כרטיסיות'
			];
			settingString = 'הגדרות';
			break;

		case 'id_ID':
			ourModuleTitles = [
				'Iklan',
				'Iklan',
				'Tulisan Pilihan Slider',
				'Posting',
				'Standar Umpan Blog',
				'Mason Feed Blog',
				'Kiriman Carousel',
				'Tab',
				'Posting dengan Tab'
			];
			settingString = 'Pengaturan';
			break;

		case 'it_IT':
			ourModuleTitles = [
				'Anno Domini',
				'Annunci',
				'In primo piano Post Slider',
				'Messaggi',
				'Blog Feed Standard',
				'Blog Feed Massoneria',
				'Post Carousel',
				'linguetta',
				'Messaggi a schede'
			];
			settingString = 'Impostazioni';
			break;

		case 'ja':
			ourModuleTitles = [
				'広告',
				'広告',
				'注目の投稿スライダー',
				'投稿',
				'ブログフィード標準',
				'ブログフィード石積み',
				'カルーセル',
				'タブ',
				'タブ付き投稿'
			];
			settingString = '設定';
			break;

		case 'ko_KR':
			ourModuleTitles = [
				'광고',
				'광고',
				'추천 게시물 슬라이더',
				'게시물',
				'블로그 피드 표준',
				'블로그 피드 벽돌',
				'게시물 회전식 메뉴',
				'탭',
				'탭이 달린 게시물'
			];
			settingString = '설정';
			break;

		case 'ms_MY':
			ourModuleTitles = [
				'Iklan',
				'Iklan',
				'Post Pilihan Slider',
				'Posts',
				'Standard Feed Blog',
				'Blog Feed Masonry',
				'Carousel Posts',
				'Tab',
				'Post Tab'
			];
			settingString = 'Tetapan';
			break;

		case 'nb_NO':
			ourModuleTitles = [
				'annonse',
				'Annonser',
				'Utvalgte innlegg Slider',
				'innlegg',
				'Blog Feed Standard',
				'Blog Feed Masonry',
				'Innlegg Karusell',
				'Tab',
				'Tabbed Innlegg'
			];
			settingString = 'innstillinger';
			break;

		case 'nl_NL':
			ourModuleTitles = [
				'Advertentie',
				'advertenties',
				'Aanbevolen berichten schuifregelaar',
				'berichten',
				'Blog Feed-standaard',
				'Blog Feed metselwerk',
				'Berichten Carousel',
				'tab',
				'Getabletteerde berichten'
			];
			settingString = 'instellingen';
			break;

		case 'pt_BR':
			ourModuleTitles = [
				'de Anúncios',
				'Publicidades',
				'Posts em Destaque Slider',
				'Postagens',
				'Padrão de feed do blog',
				'Blog Alimentar Alvenaria',
				'Carousel de Postagens',
				'Aba',
				'Postagens com guias'
			];
			settingString = 'configurações';
			break;

		case 'ro_RO':
			ourModuleTitles = [
				'Anunț',
				'Anunțuri',
				'Featured Posts Slider',
				'Mesaje',
				'Blog Feed Standard',
				'Blog Feed Masonerie',
				'Mesaje carusel',
				'Tab',
				'Tabela de mesaje'
			];
			settingString = 'Setări';
			break;

		case 'ru_RU':
			ourModuleTitles = [
				'Объявление',
				'Объявления',
				'Избранные сообщения Slider',
				'Сообщений',
				'Стандартный канал блога',
				'Blog Feed Masonry',
				'Карусель сообщений',
				'табуляция',
				'Сообщения с вкладками'
			];
			settingString = 'Настройки';
			break;

		case 'sk_SK':
			ourModuleTitles = [
				'reklama',
				'Reklamy',
				'Najlepšie príspevky Posuvník',
				'príspevky',
				'Blog Feed Standard',
				'Blog Feed Masonry',
				'Príspevky Carousel',
				'pútko',
				'Tabbed Posts'
			];
			settingString = 'Nastavenia';
			break;

		case 'sr_RS':
			ourModuleTitles = [
				'Ад',
				'Огласи',
				'Феатуред Постс Слидер',
				'Постс',
				'Стандард за храњење блога',
				'Блог Феед Масонри',
				'Постс Цароусел',
				'Таб',
				'Таббед Постс'
			];
			settingString = 'Podešavanja';
			break;


		case 'sv_SE':
			ourModuleTitles = [
				'A.d',
				'annonser',
				'Utvalda inlägg Slider',
				'inlägg',
				'Blog Feed Standard',
				'Blog Feed Masonry',
				'Inlägg Carousel',
				'Flik',
				'Tabbed Posts'
			];
			settingString = 'inställningar';
			break;

		case 'th':
			ourModuleTitles = [
				'การโฆษณา',
				'โฆษณา',
				'กระทู้แนะนำ',
				'โพสต์',
				'บล็อกฟีดมาตรฐาน',
				'บล็อกฟีดก่ออิฐ',
				'โพสต์ม้าหมุน',
				'แถบ',
				'โพสต์แบบแท็บ'
			];
			settingString = 'การตั้งค่า';
			break;

		case 'tl':
			ourModuleTitles = [
				'Ad',
				'Mga ad',
				'Mga Tampok na Mga Post Slider',
				'Mga post',
				'Blog Feed Standard',
				'Blog Feed Pagmamason',
				'Mga post Carousel',
				'Tab',
				'Nakatakdang Post'
			];
			settingString = 'Mga Setting ng';
			break;

		case 'tr_TR':
			ourModuleTitles = [
				'ilan',
				'Reklamlar',
				'Öne Çıkanlar',
				'Mesajlar',
				'Blog Yayını Standardı',
				'Blog Feed Duvarcılık',
				'Carousel Yazıları',
				'çıkıntı',
				'Sekmeli Mesajlar'
			];
			settingString = 'Ayarlar';
			break;

		case 'uk':
			ourModuleTitles = [
				'Ad',
				'Оголошення',
				'Популярні повзунки повідомлень',
				'Повідомлення',
				'Стандарт подачі блогу',
				'Блог Потік масонства',
				'Повідомлення Карусель',
				'Tab',
				'Повідомлення з вкладками'
			];
			settingString = 'налаштування';
			break;

		case 'vi':
			ourModuleTitles = [
				'Quảng cáo',
				'Quảng cáo',
				'Bài viết nổi bật Thanh trượt',
				'Bài viết',
				'Tiêu chuẩn nguồn cấp dữ liệu blog',
				'Blog Thức ăn Masonry',
				'Bài viết Carousel',
				'Chuyển hướng',
				'Bài viết theo thẻ'
			];
			settingString = 'Cài đặt';
			break;

		case 'zh_CN':
			ourModuleTitles = [
				'广告',
				'广告',
				'精选帖子滑块',
				'帖子',
				'博客Feed标准',
				'博客饲料砌体',
				'帖子转盘',
				'标签',
				'标签帖子'
			];
			settingString = '设置';
			break;

		case 'en_US':
		case 'he_IL':
		case 'bg_BG':
		case 'id_ID':
		case 'it_IT':
		case 'ru_RU':
		case 'th':
		case 'tl':
		default:
			ourModuleTitles = [
				'Ad', // lang - includes/modules.php:3095
				'Ads', // lang - includes/modules.php:2763
				'Featured Posts Slider', // lang - includes/modules.php:1904
				'Posts', //lang - includes/modules.php:11 includes/modules.php:246 includes/modules.php:900
				'Blog Feed Standard', // lang - includes/modules.php:2092
				'Blog Feed Masonry', // lang - includes/modules.php:2516
				'Posts Carousel', // lang - includes/modules.php:1642
				'Tab', // lang - includes/modules.php:921 includes/modules.php:1013 includes/modules.php:1477
				'Tabbed Posts' //lang -  includes/modules.php:890
			];
			settingString = 'Settings'; //functions.php:6032
			break;
	}

	var $node = jQuery(node);
	var $modal = $node.closest('.et-fb-modal');
	var modalTitle = $modal.find('.et-fb-modal__title').text();

	if ( modalTitle.endsWith(' ' + settingString ) ) {
		var moduleTitle = modalTitle.substring(0, modalTitle.length - settingString.length - 1 );
		if (ourModuleTitles.indexOf(moduleTitle) !== -1) {
			$node.remove();
			$modal.addClass('ags-divi-extras-module-settings');
		}
	}

});