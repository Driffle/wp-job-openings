jQuery(function ($) {
	var $content_wrapper = $('#awsm-job-response');
	// ========== Job Filters ==========
	var $filter = $('#awsm-job-filter');
	var $filter_option = $filter.find('.awsm-filter-option');

	function awsm_job_filters() {
		$.ajax({
			url: $filter.attr('action'),
			beforeSend: function(xhr) {
				$content_wrapper.addClass('awsm-jobs-loading');
			},
			data: $filter.serialize(),
			type: $filter.attr('method')
		}).done(function (data) {
			$content_wrapper.html(data);
		}).fail(function (xhr) {
			console.log(xhr);
		}).always(function() {
			$content_wrapper.removeClass('awsm-jobs-loading');
		});
	}

	function filter_check() {
		var check = false;
		if ($filter.length > 0) {
			$filter_option.each(function (i) {
				if ($(this).val().length > 0) {
					check = true;
				}
			});
		}
		return check;
	}

	if (filter_check()) {
		awsm_job_filters();
	}

	$('#awsm-job-filter .awsm-filter-option').on('change', function (e) {
		e.preventDefault();
		awsm_job_filters();
	});

	// ========== Job Listings Load More ==========
	$content_wrapper.on('click', '.awsm-load-more-btn', function (e) {
		e.preventDefault();
		var $button = $(this);
		$button.prop('disabled', true);
		var wp_data = [];
		var paged = $button.data('page');
		paged = (typeof paged == 'undefined') ? 1 : paged;

		// filters
		if ($filter.length > 0) {
			if (filter_check()) {
				wp_data = $filter_option.serializeArray();
			}
		}

		// taxonomy archives
		if(awsmJobsPublic.is_tax_archive) {
			var taxonomy = $content_wrapper.data('taxonomy');
			var term_id = $content_wrapper.data('termId');
			if(typeof taxonomy !== 'undefined' && typeof term_id !== 'undefined') {
				wp_data.push({
					name: 'awsm_job_spec[' + taxonomy + ']',
					value: term_id
				});
			}
		}

		wp_data.push({ name: 'action', value: 'loadmore' }, { name: 'paged', value: paged });

		// now, handle ajax
		$.ajax({
			url: awsmJobsPublic.ajaxurl,
			data: $.param(wp_data),
			type: 'POST',
			beforeSend: function (xhr) {
				$button.text(awsmJobsPublic.i18n.loading_text);
			}
		}).done(function (data) {
			if (data) {
				$('.awsm-load-more-main').remove();
				$content_wrapper.append(data);
			} else {
				$button.remove();
			}
		}).fail(function (xhr) {
			console.log(xhr);
		});
	});

	/*----- Custom select box - selectric -----*/
	function awsmDropDown($elem) {
		$elem.selectric({
			arrowButtonMarkup: '<span class="awsm-selectric-arrow-drop">&#x25be;</span>',
			customClass: {
				prefix: 'awsm-selectric',
				camelCase: false
			},
		});
	}
	awsmDropDown($('.awsm-job-select-control'));
	awsmDropDown($('.awsm-filter-item select'));
});