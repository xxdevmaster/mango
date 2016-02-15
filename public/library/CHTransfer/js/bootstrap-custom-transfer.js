(function($){
    //help1
    $.fn.bootstrapTransfer = function(options) {
        var settings = $.extend({}, $.fn.bootstrapTransfer.defaults, options);
        var _this;
        /* #=============================================================================== */
        /* # Expose public functions */
        /* #=============================================================================== */
        this.populate = function(input,tinput) { _this.populate(input,tinput); };
        this.set_values = function(values) { _this.set_values(values); };
        this.getTargetValues = function() { _this.get_target_values(); };
        this.get_values = function() { return _this.get_values(); };
        this.initialize_target = function(values) { _this.initialize_target(values); };
        return this.each(function(){
            _this = $(this);
            /* #=============================================================================== */
            /* # Add widget markup */
            /* #=============================================================================== */
            _this.append($.fn.bootstrapTransfer.defaults.template);
            _this.addClass("bootstrap-transfer-container");
            /* #=============================================================================== */
            /* # Initialize internal variables */
            /* #=============================================================================== */
            _this.$filter_input = _this.find('.filter-input');
            _this.$filter_input_target = _this.find('.filter-input-target');
            _this.$glyphicon_search = _this.find('.glyphicon-search');
            _this.$remaining_select = _this.find('select.remaining');
            _this.$target_select = _this.find('select.target');
            _this.$add_btn = _this.find('.selector-add');
            _this.$remove_btn = _this.find('.selector-remove');
            _this.$choose_all_btn = _this.find('.selector-chooseall');
            _this.$clear_all_btn = _this.find('.selector-clearall');
            _this._remaining_list = [];
            _this._target_list = [];
            /* #=============================================================================== */
            /* # Apply settings */
            /* #=============================================================================== */
            /* target_id */
            if (settings.target_id != '') _this.$target_select.attr('id', settings.target_id);
            /* height */
            _this.find('select.filtered').css('height', settings.height);
            /* #=============================================================================== */
            /* # Wire internal events */
            /* #=============================================================================== */
            _this.$add_btn.click(function(){
                _this.move_elems(_this.$remaining_select.val());
                return false;
            });
            _this.$remove_btn.click(function(){
			 var values =  _this.$target_select.val();
			 var e = [];
			 console.log(values);
				for (var j in values) {
				e = values[j];
					console.log(_this._target_list[e]);
					delete _this._target_list[e];
					if (_this._remaining_list[e])
						_this._remaining_list[e][0]['status']=true;
				}
				
				_this.update_lists(false);
                return false;
            });
            _this.$choose_all_btn.click(function(){
                _this.move_all();
                return false;
            });	
            _this.$clear_all_btn.click(function(){
				_this._target_list = [];
				for (var i in _this._remaining_list) {
						_this._remaining_list[i][0]['status'] = true;
					
				}
				_this.update_lists(false);
				
                return false;
            });
			_this.$filter_input.keyup(function(){
                _this.update_lists(true);
                return false;
            });
			_this.$filter_input_target.keyup(function(){
                _this.update_lists(true);
                return false;
            });
			_this.$glyphicon_search.click(function(){
                _this.update_lists(true);
                return false;
            });
            _this.populate = function(input,tinput) {
                // input: [{value:_, content:_}]
				_this.$filter_input_target.val('');
				_this.$filter_input.val('');
                _this.$filter_input.val('');
                _this.$filter_input.val('');
                for (var i in input) {
                    var e = input[i];
					_this._remaining_list[e.value] = [];
                    _this._remaining_list[e.value] = [{value:e.value, content:e.content, status:e.status}];
					
					}
				for (var i in tinput) {
                    var te = tinput[i];
                    _this._target_list[te.value] = [];
                    _this._target_list[te.value] = [{value:te.value, content:te.content}];
                }
				_this.update_lists(true);
            };
            _this.get_target_values = function() {
                return _this._target_list;
            };
           
            _this.update_lists = function() {
               _this.$remaining_select.empty();
                _this.$target_select.empty();
				var dis = "";
				for (var i in _this._remaining_list) {
						e = _this._remaining_list[i];
							dis = (e[0].status == false  || e[0].status=="false")?'disabled="disabled"':'';
                            _this.$remaining_select.append('<option class="'+ e[0].status +'" value="' + e[0].value + '" '+dis+'>' + e[0].content + '</option>');
                }
				for (var i in _this._target_list) {
							e = _this._target_list[i];
                            _this.$target_select.append('<option value="' + e[0].value + '">' + e[0].content + '</option>');
                }
				 _this.$remaining_select.find('option').each(function() {
                    var inner = _this.$filter_input.val().toLowerCase();
                    var outer = $(this).html().toLowerCase();
                    if (outer.indexOf(inner) == -1) {
                       $(this).remove();
                    }
                })
				 _this.$target_select.find('option').each(function() {
                    var inner_target = _this.$filter_input_target.val().toLowerCase();
                    var outer_target = $(this).html().toLowerCase();
                    if (outer_target.indexOf(inner_target) == -1) {
                        $(this).remove();
                    }
                })
            };
            _this.move_elems = function(values) {
                for (var i in values) {
                    val = values[i];
					if (_this._remaining_list[val][0]['status']){
                    _this._target_list[val] = _this._remaining_list[val];
					_this._remaining_list[val][0]['status'] = false;
					}
                }
                _this.update_lists(false);
            };
            _this.move_all = function() {
                for (var i in _this._remaining_list) {
					if (_this._remaining_list[i][0]['status']){
                    _this._target_list[_this._remaining_list[i][0]['value']] = _this._remaining_list[i];
					_this._remaining_list[i][0]['status'] = false;
					}
					
                }
                _this.update_lists(false);
            };
            _this.data('bootstrapTransfer', _this);
            return _this;
        });
    };
    $.fn.bootstrapTransfer.defaults = {
        'template':
            '<div class="input-group countriesBoxSiza pull-left">\
				<span class="input-group-btn"><span class="btn btn-default "><i class="fa ion-search "></i></span>\
				</span>\
                <input type="text" class="filter-input form-control">\
            </div>\
            <div style="width:10%;float:left;">&nbsp;</div>\
             <div class="input-group  countriesBoxSiza  pull-left">\
				<span class="input-group-btn"><span class="btn btn-default "><i class="fa ion-search "></i></span>\
				</span>\
                <input type="text" class="filter-input-target form-control">\
            </div>\
            <div style="clear:both;">&nbsp;</div>\
            <div class="selector-available">\
                    <select multiple="multiple" class="filtered remaining changebut"></select>\
                <a href="#" class="selector-chooseall changebut">Add All </a>\
            </div>\
            <div class="selector-chooser text-center" style="width:10%;">\
                <button class="btn btn-default btn-sm selector-add changebut"><i class="ion-log-out"></i></button>\
                <button class="btn btn-default btn-sm selector-remove changebut" href="#" ><i class="ion-close"></i></button>\
            </div>\
            <div class="selector-chosen">\
                    <select multiple="multiple" class="filtered target"></select>\
                <a href="#" class="selector-clearall changebut">Clear All </a>\
            </div>\
                    ',
        'height': '10em',
        'hilite_selection': true,
        'target_id': ''
    }
})(jQuery);