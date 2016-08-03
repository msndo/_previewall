(function($) {
	$(function() {
		var $elemList = $('#list-url');

		$elemList.find('a').each(function() {
			var classnameClicked  = 'current-selected';

			var $elemCtrl = $(this);
			$elemCtrl.on('click.markClicked', function(ev) {
				var $elemCtrl = $(this);
				var $elemRow = $elemCtrl.closest('tr');

				$elemList.find('.' + classnameClicked).removeClass(classnameClicked);
				$elemRow.addClass(classnameClicked);
			});

		});

		$('#list-url').find('.cell-title-row a').triggerOpenWinAll();

		$('#list-url').find('.col-data a').each(function() {
			var $elemCtrl = $(this);
			$elemCtrl.on('click.openLink', function(ev) {
				openBlankWinWithPopUp($(this).attr('href'), $(this).parent().data('colidx'));
				ev.preventDefault();
				return false;
			});
		});

		$('#section-manualinput-param').find('.content-ctrl').each(function(ix) {
			var settings = {
				easing: 'easeOutExpo',
				duration: 700
			};

			var $elemCtrl = $(this);

			var $elemMenu = $('#section-manualinput-param');

			$elemCtrl.on('click.openclose', function(ev) {
				var statusElemMenu = $elemMenu.get(0).getAttribute('data-openclose');
				if(statusElemMenu == 'open') {
					$elemMenu.animate({ 'right': (0 - $elemMenu.width()) + 'px' }, settings.duration, settings.easing, function() {
						$elemMenu.get(0).setAttribute('data-openclose', 'closed');
					});
				}
				else {
					$elemMenu.animate({ 'right': 0 - ''}, settings.duration, settings.easing, function() {
						$elemMenu.get(0).setAttribute('data-openclose', 'open');
					});
				}

				return false;
			});
		});

		$('#section-dd-url').recieveDragAndDrop();
		$('#form-input-url').on('submit', function() { return $(this).submitUrl(); })
	});


	// Create Blank Popup
	function openBlankWinWithPopUp(hrefTarg, nameWindow) {
		var win = window.open(
			hrefTarg
			, nameWindow
			,'width=1024\
			,height=768\
			,toolbar=yes\
			,menubar=yes\
			,resizable=yes\
			,scrollbars=yes\
			,status=yes\
			,location=yes'
		);

		if(win) { 
			win.blur();
			win.focus();
		}
	};

	$.fn.triggerOpenWinAll = function() {
		var $listElemCtrl = $(this);

		$listElemCtrl.each(function(ix) {
			var $elemCtrl = $(this);

			$elemCtrl.on('click.openLink', function(ev) {
				var $elemCtrl = $(this);

				var $seriesElemTarg = $elemCtrl.closest('tr').find('.col-data a');	
				$seriesElemTarg.each(function() {
					openBlankWinWithPopUp($(this).attr('href'), $(this).parent().data('colidx'));
					return true;
				});

				ev.preventDefault();
				return false;
			});
		});
	};

	$.fn.recieveDragAndDrop = function(options) {
		var settings = {
			selectorCtrlInputParam: '#form-input-url #ctrl-input-url',
			selectorFormInputParam: '#form-input-url'
		};

		var $elemRecieve = this;
		var objectRecieved;
		var contentRecieved;

		$elemRecieve.on('dragover', function(ev) {
			ev.preventDefault();
			$(this).addClass('dropping');
		});
		$elemRecieve.on('dragleave', function(ev) {
			ev.preventDefault();
			$(this).removeClass('dropping');
		});

		$elemRecieve.on('drop', function(ev) {
			ev.preventDefault();
			$(this).removeClass('dropping');

			objectRecieved = ev.originalEvent.dataTransfer.getData("url");

			if(! objectRecieved) { alert('Not URL'); return true; }

			$(settings.selectorCtrlInputParam).val(objectRecieved);

			$(settings.selectorFormInputParam).trigger('submit');
		});

		return this;
	};

	$.fn.submitUrl = function() {
		var $elemForm = $(this);

		var $elemInputParam = $elemForm.find('#ctrl-input-url');

		var valueParam = $elemInputParam.val();
		var seriesUrl = [];	
		var $seriesDomain = [];
		var pathUrl;

		$('.cell-header-domain').each(function(ix) {
			var $elemTarg = $(this);
			pathUrl = valueParam.replace(/^.*?\/\/.*?\//, '')
			seriesUrl.push($elemTarg.text() + pathUrl);
		})

		$.each(seriesUrl, function(ix, value) {
			openBlankWinWithPopUp(value, ix + 1);
			return true;
		});

		return false;
	};

})(jQuery);