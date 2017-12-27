//var dataFromServer = {};
jQuery.noConflict();

var dataToServer = {
    positions: {},
    css: {}
};

////plugin to get outerHTML of the element
//usage $(selector).outerHTML()
jQuery.fn.outerHTML = function(s) {
    return s
        ? this.before(s).remove()
        : jQuery("<p>").append(this.eq(0).clone()).html();
};

//plugin to get backgroundColor property as a hex number
//not as rgb(int, int, int)
jQuery.fn.getHexColor = function(rgb) {
    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    function hex(x) {
        return ("0" + parseInt(x).toString(16)).slice(-2);
    }
    return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}

//draggables elements
//add new to this array
//TODO: add class .drag to draggables
var draggables = ['[class*="drag"]'];

//droppables elements
//add new to array
//TODO: add class .drop to droppables
var droppables = ['.dropHeader', '.dropLeft', '.dropRight', '.dropFooter'];

//maybe will be useful for joomla
//disables link clicking
//var adminMode = true;

//array of Element objects
var globalDragElements = [];

//possible tabs
var globalTabs = {
    'text': {
        title: 'Text options',
        css: ['font-family', 'font-size', 'color', 'bold', 'underline', 'italic'],
        apply: ['a', 'span']
    },
    'label': {
        title: 'Label options',
        css: ['font-family', 'font-size', 'color', 'bold', 'underline', 'italic'],
        apply: ['label']
    },
    'image': {
        title: 'Image options',
        css: ['width', 'height', 'border-width', 'border-color']
    },
    'margin': {
        title: 'Margin options',
        css: ['margin-left', 'margin-bottom', 'margin-right', 'margin-top']
    }
};

//possible fields
var globalFields = {
    'font-family': {tag: 'select', values: ['Verdana', 'Arial', 'Times New Roman']},
    'font-size': {tag: 'input', type: 'text', hasPx: true},
    'color': {tag: 'input', type: 'text'},
    'bold': {tag: 'input', type: 'checkbox', name: 'font-weight', value: 'bold'},
    'underline': {tag: 'input', type: 'checkbox', name: 'text-decoration', value: 'underline'},
    'italic': {tag: 'input', type: 'checkbox', name: 'font-style', value: 'italic'},
    'width': {tag: 'input', type: 'text', hasPx: true},
    'height': {tag: 'input', type: 'text', hasPx: true},
    'border-width': {tag: 'input', type: 'text', name: 'padding', hasPx: true},
    'border-color': {tag: 'input', type: 'text', name: 'background-color'},
    'margin-left': {tag: 'input', type: 'text', hasPx: true},
    'margin-bottom': {tag: 'input', type: 'text', hasPx: true},
    'margin-right': {tag: 'input', type: 'text', hasPx: true},
    'margin-top': {tag: 'input', type: 'text', hasPx: true}
}

//possible types
//add these as an element class to the template
//{class: [tab1, tab2]}
var globalTypes = {
    dragImage: ['image', 'margin'],
    dragParam: ['text', 'label', 'margin'],
    dragText: ['text', 'margin']
}

function parseValue(val, hasPx) {
    if (hasPx) {
        return parseInt(val);
    } else {
        return val;
    }
}

function unparseValue(val, hasPx) {
    if (hasPx) {
        return val+'px';
    } else {
        return val;
    }
}

function findGlobalFieldByName(name) {
    var searchKey = '';
    if (globalFields.hasOwnProperty(name)) {
        return name;
    }
    _.each(globalFields, function(val, key) {
        if (val.name !== undefined && val.name == name) {
            searchKey = key;
        }
    })
    return searchKey;
}

/**
 * class describing the input tag
 * @param {object} options
 * @param {string} name
 * @param {string} currentValue
 * @returns {Input}
 */
/*function Input(options, name, currentValue, defaultValue, hasPx) {
    this.tag = options.tag;
    this.name = name;
    this.type = options.type;
    this.currentValue = parseValue(currentValue, hasPx);
    this.value = options.value;
    this.defaultValue = parseValue(defaultValue, hasPx);
    this.hasPx = hasPx;
    this.$el = {};
}

Input.prototype.render = function() {
    this.$el = jQuery(document.createElement(this.tag))
        .attr('name', this.name)
        .attr('type', this.type)
        .val(this.currentValue)
        .attr('value', this.currentValue);
    if (this.type == 'checkbox') {
        this.$el.attr('checked', this.currentValue == this.value ? true : false);
    }
    return this.$el.outerHTML()+(this.hasPx?'px':'');
}

Input.prototype.getValue = function($i) {
    if (this.type == 'checkbox') {
        return $i.is(':checked') ? this.value : this.defaultValue;
    } else {
        return $i.val();
    }
}

Input.prototype.getEvent = function() {
    if (this.type == 'checkbox') {
        return 'click';
    } else {
        return 'blur';
    }
}*/

/**
 * class describing the select tag
 * @param {object} options
 * @param {string} name
 * @param {string} currentValue
 * @returns {Select}
 */
var objJ = {
    Select: function (options, name, currentValue, defaultValue){
        this.tag = options.tag;
        this.name = name;
        this.type = options.type;
        this.currentValue = currentValue;
        this.values = options.values;
        this.defaultValue = defaultValue;
        this.$el = {};
        this.render = function() {
            this.$el = jQuery(document.createElement(this.tag)).attr('name', this.name);
            var options = _.union(this.values, [this.defaultValue]);
            for (var i in options) {
                var opt = options[i];
                var $option = {};
                if (this.currentValue == opt) {
                    $option = jQuery(document.createElement('option'))
                        .val(this.currentValue)
                        .text(this.currentValue + '(current)')
                        .attr('selected', true);
                }else {
                    if(typeof opt == 'string'){
                        $option = jQuery(document.createElement('option')).val(opt).text(opt);
                    }
                }
                this.$el.append($option);
            }
            return this.$el.outerHTML();
        }
        this.getValue = function($s) {
            return $s.find(':selected').val();
        }
        this.getEvent = function() {
            return 'change';
        }
    },
    Input: function (options, name, currentValue, defaultValue, hasPx){
        this.tag = options.tag;
        this.name = name;
        this.type = options.type;
        this.currentValue = parseValue(currentValue, hasPx);
        this.value = options.value;
        this.defaultValue = parseValue(defaultValue, hasPx);
        this.hasPx = hasPx;
        this.$el = {};
        this.render = function() {
            this.$el = jQuery(document.createElement(this.tag))
                .attr('name', this.name)
                .attr('type', this.type)
                .val(this.currentValue)
                .attr('value', this.currentValue);
            if (this.type == 'checkbox') {
                this.$el.attr('checked', this.currentValue == this.value ? true : false);
            }
            return this.$el.outerHTML()+(this.hasPx?'px':'');
        }
        this.getValue = function($i) {
            if (this.type == 'checkbox') {
                return $i.is(':checked') ? this.value : this.defaultValue;
            } else {
                return $i.val();
            }
        }
        this.getEvent = function() {
            if (this.type == 'checkbox') {
                return 'click';
            } else {
                return 'blur';
            }
        }
    }


}
/*function Select(options, name, currentValue, defaultValue) {
    this.tag = options.tag;
    this.name = name;
    this.type = options.type;
    this.currentValue = currentValue;
    this.values = options.values;
    this.defaultValue = defaultValue;
    this.$el = {};
}

Select.prototype.render = function() {
    this.$el = jQuery(document.createElement(this.tag)).attr('name', this.name);
    var options = _.union(this.values, [this.defaultValue]);
    for (var i in options) {
        var opt = options[i];
        var $option = {};
        if (this.currentValue == opt) {
            $option = jQuery(document.createElement('option'))
                .val(this.currentValue)
                .text(this.currentValue + '(current)')
                .attr('selected', true);
        } else {
            $option = jQuery(document.createElement('option'))
                .val(opt)
                .text(opt);
        }
        this.$el.append($option);
    }
    return this.$el.outerHTML();
}

Select.prototype.getValue = function($s) {
    return $s.find(':selected').val();
}

Select.prototype.getEvent = function() {
    return 'change';
}*/

/**
 * @class {Element}
 */
function jElement() {
    /** jquery element*/
    this.$el = {};
    this.type = '';
    this.tabs = [];
    this.qtip = {};
    /** here we will save all changed css properties. Some sort of cache*/
    this.css = {};
}

/**
 * Initialize method
 * @param {Object} el Jquery selector
 * @param {Object} qtip qtip instance
 */
jElement.prototype.init = function(el, qtip) {
    this.$el = el;
    this.qtip = qtip;
    this.type = _.intersection(_.keys(globalTypes), this.$el.attr('class').split(' '));
    this.tabs = globalTypes[this.type];
    this.save();
}

jElement.prototype.setModalContent = function(tabs, content) {
    //console.log(content);
    jQuery('#modal').html(_.template(jQuery('#modalTemplate').html(), {
        tabs: tabs,
        content: content
    }));

}

/**
 * Resets content from modal window
 */
jElement.prototype.removeTooltipContent = function() {
    this.setModalContent('', '');
}

/**
 * Helper function for rendering tab content
 * renders tabs structure
 * @param {string} tab @see globalTabs
 */
jElement.prototype.renderTab = function(tab) {
        var $li = jQuery('<li class="on_tab ui-state-default ui-corner-top"></li>');

        var $a = jQuery('<a>' + globalTabs[tab].title + '</a>').attr({'id': '#tab' + tab, 'class': 'ui-tabs-anchor'});//redirect
        $li.append($a);
    //console.log($a[0]);
        return $li.outerHTML();
}

jElement.prototype.getSetProperty = function(css) {
    var setProperty;
    //alert(css);///тут стили попадают в всплыв. окно!
    switch (css) {
        case 'color':
            setProperty = this.$el.css(css);
            setProperty = this.$el.getHexColor(setProperty);
            break;
        case 'borderColor':
            setProperty = this.$el.css('borderTopColor');
            setProperty = this.$el.getHexColor(setProperty);
            break;
        case 'borderWidth':
        case 'borderHeight':
            setProperty = this.$el.css(css.substr(0, 6)+'Top'+css.substr(6));
            break;
        case 'width':
        case 'height':
            setProperty = this.$el[css]();
            break;
        case 'backgroundColor':
            setProperty = this.$el.css(css);
            setProperty = this.$el.getHexColor(setProperty);
            break;
        case 'padding':
            setProperty = this.$el.find('img:first').css('padding-top');
            break;
        default:
            setProperty = this.$el.css(css);
            break;
    }

    return setProperty;
}

jElement.prototype.getParamsForRenderTag = function(css) {
    var obj = globalFields[css];
    var cssName = obj.name ? obj.name : css;
    var jQueryCss = jQuery.camelCase(cssName);
    var setProperty = this.getSetProperty(jQueryCss);
    var className = obj.tag.charAt(0).toUpperCase() + obj.tag.substr(1);
    var hasPx = obj.hasOwnProperty('hasPx')?obj.hasPx : false;
    return {
        obj: obj,
        cssName: cssName,
        jQueryCss: jQueryCss,
        setProperty: setProperty,
        className: className,
        hasPx: hasPx
    };
}

/**
 * @param {type} css
 * @returns {jQuery} tag object(input or select)
 */
jElement.prototype.renderTag = function(css) {
    var params = this.getParamsForRenderTag(css);
    return new objJ[params.className](params.obj, params.cssName, params.setProperty, this.css[params.cssName], params.hasPx);
    //return new window[params.className](params.obj, params.cssName, params.setProperty, this.css[params.cssName], params.hasPx);
}

/**
 * Render tab content
 * _.template @see http://underscorejs.org/#template
 * @param {int} index
 * @param {string} tab
 * @returns {string}
 */
jElement.prototype.renderTabContent = function(tab) {
    if(typeof tab == 'string'){
        var css = globalTabs[tab].css; ////!!!!!!!!!!!!!!
        //console.log(css);
        var $container = jQuery('<div></div>').attr('id', 'tab' + tab);
        $container.append('<table></table>');
        var compiled = "";
        for (var i in css) {
            if(typeof css[i] == 'string'){
                var input = this.renderTag(css[i]);
                //console.log(input);
                compiled = _.template(jQuery('#inputTemplate').html(), {
                    name: css[i],
                    labelText: css[i],
                    element: input.render()
                });
                $container.find('table').append(compiled);
            }
        }
            $container.append("<script>"+
			"jQuery('#tabmargin').css('display','none');if(jQuery('#tablabel').length){jQuery('#tablabel').css('display','none');}jQuery('.on_tab:first').addClass('ui-tabs-active ui-state-active');" +
                "if(jQuery('#tabtext').length){jQuery('#tabtext').css('display','block');}else{jQuery('#tabimage').css('display','block');}jQuery('.on_tab').click(function(){" +
                "" +
                "if(this.children[0].id == '#tabtext'){" +
                "jQuery('#tabmargin').css('display','none');if(jQuery('#tablabel').length){jQuery('#tablabel').css('display','none');jQuery('.on_tab').eq(-2).removeClass('ui-tabs-active ui-state-active');}jQuery('#tabtext').css('display','block');this.addClass('ui-tabs-active ui-state-active');jQuery('.on_tab:last').removeClass('ui-tabs-active ui-state-active');}" +
                "else if(this.children[0].id == '#tabimage'){" +
				"jQuery('#tabmargin').css('display','none');if(jQuery('#tablabel').length){jQuery('#tablabel').css('display','none');}jQuery('#tabimage').css('display','block');this.addClass('ui-tabs-active ui-state-active');jQuery('.on_tab:last').removeClass('ui-tabs-active ui-state-active');}" +
				
				"else if(this.children[0].id == '#tabmargin'){" +
                "if(jQuery('#tabtext').length){jQuery('#tabtext').css('display','none');if(jQuery('#tablabel').length){jQuery('#tablabel').css('display','none');}}else{jQuery('#tabimage').css('display','none');}jQuery('#tabmargin').css('display','block');this.addClass('ui-tabs-active ui-state-active');jQuery('.on_tab:first').removeClass('ui-tabs-active ui-state-active');}" +
                "else if(this.children[0].id == '#tablabel'){" +
				"jQuery('#tabmargin').css('display','none');jQuery('#tabtext').css('display','none');jQuery('#tablabel').css('display','block');this.addClass('ui-tabs-active ui-state-active');jQuery('.on_tab:last').removeClass('ui-tabs-active ui-state-active');jQuery('.on_tab:first').removeClass('ui-tabs-active ui-state-active');}" +
				"});</script>");
            return $container.outerHTML();

    }
}
jElement.prototype.getCurrentTab = function() {
    return jQuery('#modal #tabs [id^="tab"]').not(':hidden');
}

jElement.prototype.attachCustomEvent = function() {
    var that = this;
    jQuery.each(jQuery("#tabs input, #tabs select"), function(index, item) {
        var $el = jQuery(item);
        var searchKey = '';
        searchKey = findGlobalFieldByName($el.prop("name"));
        var input = that.renderTag(searchKey);
        $el.on(input.getEvent(), function(event) {
            that.applyStyles(jQuery(this).attr('name'), input.getValue(jQuery(this)));
        })
    })
}

jElement.prototype.applyStyles = function(name, value) {
    var tab = globalTabs[this.getCurrentTab().attr('id').substr(3)];
    if (name == 'width' || name == 'height') {
        var intV = parseInt(value);
        if (intV != this.$el[name]()) {
            var oName = name == 'width'?'height':'width';
            var img = this.$el.find('img:first');
            this.$el[name](this.$el[name]());
            this.$el[oName](this.$el[oName]());
            var paddingValue = parseInt(img.css('padding-top'));
            var elNameVal = this.$el[name]()-2*paddingValue;
            var elONameVal = this.$el[oName]()-2*paddingValue;
            var intVPadding = intV - 2*paddingValue;
            var curImg = {
                'width': img.width(), 
                'height': img.height()
            };
            var nextImg = {
                'width': '',
                'height': ''
            }
            if (curImg[name] / intVPadding < curImg[name] / elNameVal) {
                nextImg[name] = intVPadding/elNameVal*curImg[name];
                nextImg[oName] = nextImg[name]/curImg[name]*curImg[oName];
                if (nextImg[oName] > elONameVal) {
                    nextImg[name] = nextImg[name] * elONameVal/nextImg[oName];
                    nextImg[oName] = elONameVal;
                }
            } else {
                nextImg[name] = intVPadding/elNameVal*curImg[name];
                nextImg[oName] = nextImg[name]/curImg[name]*curImg[oName];
                if (nextImg[name] > elNameVal) {
                    nextImg[name] = nextImg[name] * elONameVal/nextImg[oName];
                    nextImg[oName] = elONameVal;
                }
            }
            img.css(nextImg);
            var m = Math.floor(((name=='height'?intVPadding:elONameVal) - img.height())/2);
            var mp = m+"px";
            img.css("margin-top",mp);
        }
    }
    if (name == 'padding') {
        var val = parseInt(value);
        var img = this.$el.find('img:first');
        if (val != parseInt(img.css('padding-top'))) {
            var elWidth = this.$el.width();
            var elHeight = this.$el.height();
            var nextImg = {
                width: img.width()*(elWidth-2*val)/elWidth,
                height: img.height()*(elHeight-2*val)/elHeight,
                padding: val+'px'
            }
            img.css(nextImg);
            var m = Math.floor((this.$el.height()-2*val - img.height())/2);
            var mp = m+"px";
            img.css("margin-top",mp);
        }
    } else {
        var gf = globalFields[findGlobalFieldByName(name)];
        var $el = this.$el;
        if (tab.hasOwnProperty('apply')) {
            _.each(tab.apply, function(item){
                var $item = $el.find(item);
                $item.css(jQuery.camelCase(name), gf.hasOwnProperty('hasPx')?unparseValue(value, true):value);
            })
        } else {
            $el.css(jQuery.camelCase(name), gf.hasOwnProperty('hasPx')?unparseValue(value, true):value);
        }
    }
}

/**
 * Renders modal window with tabs and content
 */
jElement.prototype.renderTooltipContent = function() {
    this.removeTooltipContent();

    //render content for each tab
    var tabsList = '', tabsContent = '';

    for (tab in this.tabs) {
        if(typeof this.tabs[tab] == 'string'){
            tabsList += this.renderTab(this.tabs[tab]);
            tabsContent += this.renderTabContent(this.tabs[tab]);
        }

    }
//(tabsContent);
    //tabsContent+"<script>alert('fgd');</script>";
    //var script1 = "<script>jQuery('.on_tab').click(function(){console.log(this);});</script>";
    this.setModalContent(tabsList, tabsContent);
    this.attachCustomEvent();
    jQuery('#tabs').tabs();
    jQuery('#modal').dialog({
        height: 'auto',
        modal: true,
        draggable: true,
        resizable: false,
        width: 'auto'
    });
    //<script>window.close();</script>
}

//TODO
jElement.prototype.save = function() {

    _.each(this.tabs, function(tab, index) {
        _.each(globalTabs[tab].css, function(cssIndex) {
            var cssName = globalFields[cssIndex].name ? globalFields[cssIndex].name : cssIndex;
            this.css[cssName] = this.$el.css(jQuery.camelCase(cssName));
        }, this)
    }, this);
}

jElement.prototype.getChangedCss = function() {
    var changedCss = {};
    _.each(this.tabs, function(tab, index) {
        _.each(globalTabs[tab].css, function(cssIndex) {
            var cssName = globalFields[cssIndex].name ? globalFields[cssIndex].name : cssIndex;
            var value = this.$el.css(jQuery.camelCase(cssName));
            //if (value != this.css[cssName]) { //tratata
                changedCss[cssName] = value;
            //}
        }, this)
    }, this);
    return changedCss;
}

jElement.prototype.getCss = function() {
    return this.css;
}

jElement.prototype.getPositions = function() {
    return _.filter(droppables, function(drop) {
        return jQuery(drop).find(this.$el).length;
    }, this);
}

jElement.prototype.getDataIndex = function() {
    return this.$el.data('index');
}

jQuery(document).ready(function() {
    init(draggables, droppables);

    /** click drag link from tooltip */
    jQuery('body').on('click', '[href="#drag"]', function(e) {
        e.preventDefault();
        enableDragDrop(draggables, droppables);
    })
    /** click showparams link from tooltip */
    jQuery('body').on('click', '[href="#showParams"]', function(e) {
        e.preventDefault();
        /** what element should we use for rendering modal */
        var tooltip = _.find(globalDragElements, function(item) {
            var elements = item.qtip.qtip('api').elements;
            return jQuery(elements.tooltip).has(jQuery(this)).length;
        }, this);
        //console.log(tooltip);
        tooltip.renderTooltipContent();

    })

    //alert(dump(dataFromServer, 'dataFromServer'));
    jQuery('#save').click(function(e) {
        e.preventDefault();
        _.each(droppables, function(item) {
            dataToServer.positions[item] = new Array();
        })
        _.each(globalDragElements, function(item) {
            var index = item.getDataIndex();
//alert(dump(item, 'item'));
            //jQuery(".success").after(dump(item, 'item'));
            dataToServer.positions[item.getPositions()[0]].push(index);
            dataToServer.css[index] = item.getChangedCss();
        })
        //console.log(dataToServer);
		var url_ajax = window.location.pathname+'/index.php?option=com_jbcatalog&task=save_position&tmpl=component';
        jQuery.ajax({
           // url: 'data.php',
            url: url_ajax,
            type: 'POST',
            data: {data: JSON.stringify(dataToServer)},
            success: function(response) {
                jQuery(window).scrollTop(0);
                jQuery('.successDIV').show(100, function(){
                    var that = this;
                    (function(th){
                        setInterval(function(){
                            jQuery(th).hide();
                        }, 5000)
                    })(that);
                })
            },
            error: function(response) {
            }
        })
    })

    jQuery('#cancel').click(function(e) {
        e.preventDefault();

        window.location.reload();
    })
})

/**
 * Enables or disables dragging of the element
 * @param {Object} item Jquery element
 * @param {boolean} enable whether to enable or disable the dragging of the element
 */
function doDraggable(item, enable) {
    jQuery(item).draggable({
        containment: ".block",
        cursor: "crosshair",
        snap: ".block",
        disabled: !enable,
        helper: function() {
            if (jQuery('#draggableHelper').length == 0) {
                var $el = jQuery('<div id="draggableHelper"></div>');
                $el.appendTo(jQuery('body'));
            } else {
                $el = jQuery('#draggableHelper');
            }
            $el.append(jQuery(this).html());
            return $el;
        },
        start: function(event, ui) {
            jQuery.each(droppables, function(index, val) {
                jQuery.each(jQuery(val), function() {
                    jQuery(this).data('borderStyle', jQuery(this).css('borderStyle')).data('borderColor', jQuery(this).css('borderColor'));
                    jQuery(this).css('borderStyle', 'dotted').css('borderColor', '#ff0000');
                })
            });
        },
        stop: function(event, ui) {
            jQuery.each(droppables, function(index, val) {
                jQuery.each(jQuery(val), function() {
                    jQuery(this).css('borderStyle', jQuery(this).data('borderStyle')).css('borderColor', jQuery(this).data('borderColor'));
                })
            });
        }
    });
}

/**
 * Enables or disables dropping of the element
 * @param {Object} item Jquery element
 * @param {boolean} enable whether to enable or disable the dropping of the element
 */
function doDroppable(item, enable) {
    jQuery(item).droppable({
        drop: handleDropEvent,
        hoverClass: "hovered",
        tolerance: "pointer",
        disabled: !enable,
        accept: function() {
            return true;
        },
        over: function(event, ui) {
            ui.helper.addClass('dragOver');
        },
        out: function(event, ui) {
            ui.helper.removeClass('dragOver');
        }
    });
}

/**
 * function initializes the app,
 * forms globalDragElements array
 * @param {array} dragElements
 * @param {array} dropElements
 */
function init(dragElements, dropElements) {
    if (dataFromServer) {
        var position = {};
        _.each(dataFromServer.positions, function(item, index) {
            _.each(item, function(i) {
                jQuery("[data-index=" + i + "]").appendTo(jQuery(index));
            })
        });

        for (var i in dataFromServer.css) {
            applyCss(i, dataFromServer.css[i]);
        }
    }
    if(!adminMode){
        return false;
    }
    jQuery.each(dragElements, function(index, val) {
        jQuery.each(jQuery(val), function() {
            jQuery(this).on('click', 'a', function(e) {
                if (adminMode) {
                    e.preventDefault();
                }
                jQuery(this).parents('[class^="drag"]').trigger('click');
            })
            var qtip = jQuery(this).qtip({
                content: {
                    text: jQuery('.parameters').clone(),
                    button: true
                },
                show: {
                    event: 'click'
                },
                position: {
                    target: 'mouse',
                    adjust: {
                        mouse: false
                    }
                }
            });
            var el = new jElement;
            el.init(jQuery(this), qtip);
            globalDragElements.push(el);
        })
    });
}

function applyCss(cssClass, styles) {
    _.each(styles, function(value, style) {
        if (_.indexOf(_.keys(globalFields), style) !== -1) {
            var name = globalFields[style].name ? globalFields[style].name : style;
            jQuery('[data-index="' + cssClass + '"]').css(jQuery.camelCase(name), value);
        }
    })
}

function enableDragDrop(dragElements, dropElements) {
    jQuery.each(dragElements, function(index, val) {
        jQuery.each(jQuery(val), function() {
            doDraggable(jQuery(this), true);
        })
    });
    jQuery.each(dropElements, function(index, val) {
        jQuery.each(jQuery(val), function() {
            doDroppable(jQuery(this), true);
        })
    });
}

function disableDragDrop(dragElements, dropElements) {
    jQuery.each(dragElements, function(index, val) {
        jQuery.each(jQuery(val), function() {
            doDraggable(jQuery(this), false);
        })
    });
    jQuery.each(dropElements, function(index, val) {
        jQuery.each(jQuery(val), function() {
            doDroppable(jQuery(this), false);
        })
    });
}

/**
 * callback for drop event
 * @param {Object} event
 * @param {Object} ui
 */
function handleDropEvent(event, ui) {
    reposition(jQuery(ui.draggable), jQuery(event.target));
}

function reposition($src, $dest) {
    $src.appendTo($dest);
}
