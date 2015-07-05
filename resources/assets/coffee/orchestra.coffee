root = @
Javie = root.Javie
jQuery = root.jQuery

setup_button_group = ($) ->
	group = $(@)
	form = group.parents('form').eq(0)
	name = group.attr('data-toggle-name')
	hidden = $("input[name='#{name}']", form)
	buttons = $('button', group)

	buttons.each((i, item) ->
		button = $(item)
		set_active = ->
			button.addClass('active') if button.val() is hidden.val()
			true
		button.on('click', ->
			buttons.removeClass('active')
			hidden.val($(@).val())

			set_active()
		)
		set_active()
	)
	true

setup_helper = ($) ->
	$('input[type="date"]').datepicker({ dateFormat: "yy-mm-dd" })
	$('select.form-control[role!="agreement"], .navbar-form > select[role!="agreement"]').select2().removeClass('form-control')
	$('*[role="tooltip"]').tooltip()
	true

setup_pagination = ($) ->
	$('div.pagination > ul').each((i, item) ->
		$(item).addClass('pagination').parent().removeClass('pagination')
		true
	)
	true

setup_agreement = ($) ->
	switchers = $('select[role="agreement"]')
	switchers.removeClass('form-control')
	switchers.each((i, item) ->
		switcher = $(item)
		switcher.toggleSwitch(
			highlight: switcher.data("highlight")
			width: 25
			change: (e, target) ->
				Javie.trigger('switcher.change', [switcher, e])
				true
		)
		switcher.css('display', 'none')
		true
	)
	true

jQuery(($) ->
	setup_agreement($)
	setup_button_group($)
	setup_helper($)
	setup_pagination($)
	true
)
