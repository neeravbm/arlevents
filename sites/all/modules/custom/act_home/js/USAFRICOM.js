(function ($) {
  Drupal.behaviors.arlslider = {
    attach: function(context, settings) {
			billboards = function () {
				// global variables
				var bbContainer,
				moveTimer,
				iActiveBB,
				iMaxBBNum,
				iToggleSpeed = 1300
				iAutoToggleTimer = 7500

				var init = function () {
						// initialize billboard
						bbContainer = $("[data-billboards]");
						iMaxBBNum = bbContainer.data('billboards');
						iActiveBB = 1;

						bbContainer.hoverIntent({ interval: 120, sensitivity: 1, over: billboards.Pause, out: billboards.Resume, timeOut: 0 });

						// set bb link
						$("[data-bbUrlString]").each(function () {
								$(this).click(function () {
										window.location = $(this).attr('data-bbUrlString');
								});
						});

						// set image positions, the site uses a 16:9 area for billboards, we need to place the image inside this area based
						// on configured positioning on the billboard image.
						$("[data-bbPhoto]").each(function () {
								// each photo is expected to have these data annotations, their values are used to position the image:
								// data-bbPhoto="True" data-bbPhotoHeight="600" data-bbPhotoLeft="96" data-bbPhotoTop="31" data-bbPhotoZoom="1.43459915611814"
								var zoomFactor = parseFloat($(this).attr('data-bbPhotoZoom')),
								topOffset = parseInt($(this).attr('data-bbPhotoTop'), 10),
								leftOffset = parseInt($(this).attr('data-bbPhotoLeft'), 10),
								photoHeight = parseInt($(this).attr('data-bbPhotoHeight'), 10);
								
								var newHeight = Math.round(photoHeight * zoomFactor);
								var newTop = -(Math.round(topOffset * zoomFactor));
								var newLeft = -(Math.round(leftOffset * zoomFactor));

								$(this).css('height', newHeight);
								$(this).css('top', newTop);
								$(this).css('left', newLeft);
								$(this).css('position', 'absolute');
						});

			  // turn on a timer to rotate the billboards based on the auto toggle timer
			  moveTimer = setInterval("billboards.moveNext()", iAutoToggleTimer);

				},
				moveNext = function () {
						var hideID = iActiveBB;
						if (iActiveBB == iMaxBBNum) {
								iActiveBB = 1;
						} else {
								iActiveBB += 1
						};
						toggleBillboard(iActiveBB, hideID);
				},
				movePrev = function () {
						var hideID = iActiveBB;
						if (iActiveBB == 1) {
								iActiveBB = iMaxBBNum;
						} else {
								iActiveBB -= 1
						};
						toggleBillboard(iActiveBB, hideID);
				},
				moveTo = function (bbID) {
						var hideID = iActiveBB;
						iActiveBB = parseInt(bbID);
						toggleBillboard(iActiveBB, hideID);
				},
				Pause = function () {        
						clearInterval(moveTimer);
				},
				Resume = function () {
						//moveNext();
						moveTimer = setInterval("billboards.moveNext()", iAutoToggleTimer);
				},
				toggleBillboard = function (showBBID, hideBBID) {

			if (parseInt(showBBID) != parseInt(hideBBID)) {
							var bbShow = bbContainer.children('[data-billboard=' + showBBID + ']'),
							bbHide = bbContainer.children('[data-billboard=' + hideBBID + ']');
							bbShow.fadeIn(Math.round(iToggleSpeed / 2));
							bbHide.fadeOut(iToggleSpeed);
							$('[data-bbMenuItem=' + showBBID + ']').addClass('selected');
							$('[data-bbMenuItem=' + hideBBID + ']').removeClass('selected');
			};

				}

				return {
						init: init,
						moveNext: moveNext,
						movePrev: movePrev,
						moveTo: moveTo,
						Pause: Pause,
						Resume: Resume
				};
		} ();
		billboards.init();
	}
}
})(jQuery);

/******** Home page Slider *********/
/*(function ($) {
   Drupal.behaviors.arlslider = {
        attach: function(context, settings) {
	         billboards.init(); 
	       }
	  } 
})(jQuery);*/
