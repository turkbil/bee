(function ($, window, document, undefined) {
   'use strict';
   const pluginName = 'Builder';
   let $activeSection;
   let $activeElement;
   let $activeBlock;
   let $activeColumn;
   let $activeRow;
   let insertType = 'section';

   const wraps = {
      gridInsert: '<a aria-readonly="true" class="grid-insert"><i aria-readonly="true" class="icon plus"></i></a>',
      gridMove: '<div aria-readonly="true" class="grid-move"><a aria-readonly="true" class="up"><i aria-readonly="true" class="icon chevron up"></i></a><a aria-readonly="true" class="down"><i aria-readonly="true" class="icon chevron down"></i></a></div>'
   };

   let htmlEditor = ace.edit('tempHtml', {
      mode: 'ace/mode/html',
      theme: 'ace/theme/dracula',
      highlightActiveLine: false,
      displayIndentGuides: true,
      wrap: true,
      showPrintMargin: false
   });


   /**
    * Description
    * @method Plugin
    * @param element
    * @param options
    * @constructor
    */
   function Plugin(element, options) {

      this.element = element;
      this._name = pluginName;
      this._defaults = $.fn.Builder.defaults;
      this.options = $.extend({}, this._defaults, options);

      this.init();

   }

   $.extend(Plugin.prototype, {
      /**
       * Description
       * @method init
       * @return
       */
      init: function () {
         this._initBuilder();
         //this._closeStyler("default");
         //this._closeEditor("default");
         //this._closeAnimator("default");
         this.bindEvents();
      },

      /**
       * Description
       * @method _initBuilder
       * @return
       */
      _initBuilder: function () {
         let plugin = this;
         $(this.element).children().each(function () {
            if ($(this).not('.section').length) {
               $(this).wrap('<div class="section"></div>');
            }
         });

         $(this.element).children('.section').each(function () {
            let id = plugin.makeid();
            $(this).attr({
               'data-id': id,
               'wf-type': 'section',
               'wf-label': 'Section'
            });
            let $grid = $(this).find('.wojo-grid');
            if ($grid) {
               $grid.attr({
                  'wf-type': 'wcontainer',
                  'wf-label': 'Wojo Container'
               });
            }
            let $rows = $(this).find('.row');
            if ($rows) {
               $rows.attr({
                  'wf-type': 'row',
                  'wf-label': 'Row'
               });
            }
            let $columns = $rows.find('.columns');
            if ($columns) {
               $columns.attr({
                  'wf-type': 'columns',
                  'wf-label': 'Column'
               });
            }

            $columns.find('h1, h2, h3, h4, h5, h6, .heading, p, .text, .description').each(function () {
               let name = $(this).is('p, .text, .description') ? 'Paragraph' : 'Heading';
               let type;
               if ($(this).is('h1, h2, h3, h4, h5, h6') && $(this).children().length) {
                  type = 'container';
               } else {
                  type = 'text';
               }
               $(this).attr({
                  'wf-type': type,
                  'wf-label': name
               });
            });

            $columns.find('.card, .cards, .segment, .message, .list, .list > .item, .content, .header, .footer, blockquote').each(function () {
               $(this).addClass('container');
            });

            $columns.find('.container').each(function () {
               let name;
               if ($(this).hasClass('card')) {
                  name = 'Card';
               } else if ($(this).hasClass('cards')) {
                  name = 'Cards';
               } else if ($(this).hasClass('segment')) {
                  name = 'Segment';
               } else if ($(this).hasClass('message')) {
                  name = 'Message';
               } else if ($(this).hasClass('content')) {
                  name = 'Content';
               } else if ($(this).hasClass('header')) {
                  name = 'Header';
               } else if ($(this).hasClass('footer')) {
                  name = 'Footer';
               } else if ($(this).hasClass('list')) {
                  name = 'List';
               } else if ($(this).hasClass('item')) {
                  name = 'List Item';
               } else {
                  name = 'Container';
               }

               $(this).attr({
                  'wf-type': 'container',
                  'wf-label': name
               });
            });

            let $img = $columns.find('img');
            if ($img) {
               $img.attr({
                  'wf-type': 'img',
                  'wf-label': 'Image'
               });
            }
            let $figure = $columns.find('figure');
            if ($figure) {
               $figure.attr({
                  'wf-type': 'container',
                  'wf-label': 'Figure'
               });
            }
            let $video = $columns.find('.video');
            if ($video) {
               $video.attr({
                  'wf-type': 'video',
                  'wf-label': 'Video'
               });
            }
            let $audio = $columns.find('.soundcloud');
            if ($audio) {
               $audio.attr({
                  'wf-type': 'audio',
                  'wf-label': 'Audio'
               });
            }
            let $map = $columns.find('.google-map');
            if ($map) {
               $map.attr({
                  'wf-type': 'map',
                  'wf-label': 'Google Map'
               });
            }
            let $href = $columns.find('a');
            if ($href) {
               $href.attr({
                  'wf-type': 'link',
                  'wf-label': 'Link'
               });
            }

            $columns.find('span, .list > li').each(function () {
               $(this).attr({
                  'wf-type': 'text',
                  'wf-label': 'Text'
               });
            });

            $columns.find('.icon').each(function () {
               if ($(this).prop('tagName').toLowerCase() === 'i') {
                  $(this).attr({
                     'wf-type': 'icon',
                     'wf-label': 'Icon'
                  });
               }
            });

            let $label = $columns.find('.label');
            if ($label) {
               $label.attr({
                  'wf-type': 'label',
                  'wf-label': 'Label'
               });
            }
         });

         $(this.element).children('.section').append(wraps.gridInsert).append(wraps.gridMove);

         $(this.element).find('[wf-type]').each(function (index) {
            $(this).attr('nav-id', 'nav_' + index);
         });

         $(this.element).find('[data-image]').each(function () {
            let img = $(this).attr('data-image');
            $(this).attr('style', 'background-image: url(' + plugin.options.upurl + img + ')');
         });

         $('.is_draggable').draggable();
         $('.wojo.accordion').wAccordion();

         $('#section-helper').on('click', '.wojo.tabs a', function (event) {
            let tab_id = $(this).attr('data-tab');
            $('#section-helper .wojo.tab').removeClass('active');
            $('#section-helper .wojo.tabs li').removeClass('active');

            $(this).parent().addClass('active');
            $('#section-helper .wojo.tab > .item').removeClass('active');
            $('#' + tab_id).addClass('active');
            event.preventDefault();
         });

         $('#element-helper').css('pointer-events', 'none');

         $('#dropdown-sizeMenu').on('click', 'a.item', function () {
            let size = $(this).data('value');
            let text = $(this).text();
            $('#builder').animate({
               width: size + 'px'
            }, 650);
            $('[data-wdropdown=\'#dropdown-sizeMenu\']').find('span').text(text);
         });

         $('#mainFrame').on('click', function (event) {
            const $eh = $('#element-helper');
            if (event.target.id === 'mainFrame') {
               $(plugin.element).children().removeClass('active');
               $(plugin.element).find('.live').removeClass('live');
               $('#builderNav').removeClass('open').attr('data-id', 'none').html('');
               $('#advanced_html .button').addClass('disabled');
               $activeSection = null;
               $activeElement = null;
               $activeBlock = null;
               $activeColumn = null;
               $activeRow = null;

               $eh.css('pointer-events', 'none');
               $eh.find('.details').removeAttr('style');
            }
         });

         $('#blockFilter').on('change', function () {
            const $be = $('#builder-elements .item');
            $be.hide();
            $('#builder-elements .item a[data-type=' + $(this).val() + ']').parent().show();

            if ($(this).val() === 'all') {
               $be.show();
            }
         });

         $(document).on('click', 'a.scale', function () {
            $('#builderViewer').contents().find('#builderFrame').toggleClass('expanded compressed');
            $('.icon', this).toggleClass('out in');
         });

         // save page
         $('#saveAll').on('click', function () {
            const $td = $('#tempData');
            $('#mainFrame').addClass('wojo loading form');
            $td.html($(plugin.element).html());

            $td.find('*').removeAttr('contenteditable style wf-label wf-type nav-id data-id');
            $td.children('.section').each(function () {
               $(this).removeClass('live active');
               $(this).children('.grid-insert').remove();
               $(this).children('.grid-move').remove();
            });

            $td.find('.card, .cards, .segment, .message, .list, .content, .header, .footer, blockquote').each(function () {
               $(this).removeClass('container');
            });

            $td.find('*').removeClass('live');

            $td.find('[data-wplugin-id]').each(function () {
               let palias = $(this).attr('data-wplugin-alias');
               let pid = $(this).attr('data-wplugin-id');
               let plugin_id = $(this).attr('data-wplugin-plugin_id');
               $(this).replaceWith('%%' + palias + '|plugin|' + plugin_id + '|' + pid + '%%');
            });

            $td.find('[data-wmodule-id]').each(function () {
               let malias = $(this).attr('data-wmodule-alias');
               let mid = $(this).attr('data-wmodule-id');
               let module_id = $(this).attr('data-wmodule-module_id');
               $(this).replaceWith('%%' + malias + '|module|' + module_id + '|' + mid + '%%');
            });

            $td.find('[data-wuplugin-id]').each(function () {
               let pid = $(this).attr('data-wuplugin-id');
               $(this).replaceWith('%%user|uplugin|0|' + pid + '%%');
            });

            $td.find('*[class=""]').each(function () {
               $(this).removeAttr('class');
            });

            let html = $td.html();
            let langall = ($('input[name=langall]').is(':checked')) ? 'all' : $.url().segment(-2);

            $.ajax({
               url: plugin.options.aurl + 'builder/action/',
               dataType: 'json',
               method: 'POST',
               data: {
                  action: 'save',
                  content: html,
                  id: $.url().segment(-1),
                  lang: langall,
                  pagename: plugin.options.pagename
               }
            }).done(function (json) {
               $.wNotice({
                  autoclose: 12000,
                  type: json.type,
                  title: json.title,
                  text: json.message
               });
               $('#mainFrame').removeClass('wojo loading form');
            });
         });
      },

      /**
       * Description
       * @method bindEvents
       * @return
       */
      bindEvents: function () {
         this._onEvents();
         this._insert();
         this._editProperties();
         this._editSection();
         this._editNav();
         this._editHtml();
         this._deleteBlock();
         this._moveBlock();
         //this._copyBlock();
         //this._animateBlock();
      },

      /**
       * Description
       * @method _editProperties
       * @return
       */
      _editProperties: function () {
         this._basicSection();
         this._linkSection();
         this._iconSection();
         this._labelSection();
         this._imageSection();
         this._frameSection();
         this._paddingSection();
         this._marginSection();
         this._textSection();
         this._rowSection();
         this._columnsSection();
         this._borderSection();
         this._backgroundSection();
         this._displaySection();
         this._positionSection();
         this._advancedSection();
         this._visibilitySection();
      },

      /**
       * Description
       * @method _editSection
       * @return
       */
      _editSection: function () {
         let plugin = this,
           classes, type, attr, html;
         let props = {};
         const $bn = $('#builderNav');

         $(this.element).on('click', '.section', function (event) {
            $(plugin.element).children().removeClass('active');
            $(this).addClass('active');

            $(plugin.element).removeClass('live').find('.live').removeClass('live');
            $(event.target).addClass('live');

            $activeSection = $(this);
            let sid = $(this).data('id');
            let targetId = $(event.target).attr('nav-id');

            let el = $(this).makeNav();
            $bn.html(el).attr('data-id', sid).addClass('open');

            let $element = $bn.find('[nav-id=' + targetId + ']');
            $bn.find('div.active').removeClass('active');
            $element.children().addClass('active');

            classes = $(event.target).attr('class').split(/\s+/);
            type = $(event.target).prop('nodeName').toLowerCase();
            html = $(event.target).html();
            attr = event.target.attributes;

            $activeElement = $(event.target);

            props = {
               classes: classes,
               type: type,
               attr: attr,
               html: html,
               element: $(event.target)
            };

            let empty = $(event.target).is('.is_empty');
            let readonly = $(event.target).attr('aria-readonly');

            if (readonly || empty) {
               $activeColumn = $(event.target);
               plugin._insertPrepare(readonly, empty);
            } else {
               plugin._loadEditor(props);
               event.preventDefault();
            }
         });
      },

      /**
       * Description
       * @method _editNav
       * @return
       */
      _editNav: function () {
         let plugin = this,
           classes, type, attr, html;
         let props = {};
         const $bn = $('#builderNav');

         $bn.on('click', 'div', function (event) {
            let id = $(this).closest('li').attr('nav-id');
            let navId = $activeSection.attr('nav-id');
            let selected = $activeSection.find('[nav-id=' + id + ']');

            if ($(event.target).is('.icon')) {
               plugin._deleteBlock((id === navId) ? 'section' : 'element');
            } else {
               $activeSection.removeClass('live').find('.live').removeClass('live');
               if (id === navId) {
                  $activeSection.addClass('live');
                  classes = $activeSection.attr('class').split(/\s+/);
                  type = $activeSection.prop('nodeName').toLowerCase();
                  attr = $activeSection.get(0).attributes;
                  $activeElement = $activeSection;
                  html = '';
               } else {
                  selected.addClass('live');
                  classes = selected.attr('class').split(/\s+/);
                  type = selected.prop('nodeName').toLowerCase();
                  html = selected.html();
                  attr = selected.get(0).attributes;
                  $activeElement = selected;
               }

               $bn.find('div.active').removeClass('active');
               $(this).addClass('active');

               props = {
                  classes: classes,
                  type: type,
                  attr: attr,
                  html: html
               };

               plugin._loadEditor(props);
            }
         });
      },

      /**
       * Description
       * @method _loadEditor
       * @param props
       * @private
       */
      _loadEditor: function (props) {
         let plugin = this;
         let wftype = props.attr['wf-type'].nodeValue;
         const $ehbt = $('#element-helper textarea[name=\'basic_text\']');
         const $ehbd = $('#element-helper section[data-helper=\'basic\'] .details');
         const $b_text = $('#b_text');

         $b_text.addClass('hide-all');
         $('#b_link').addClass('hide-all');
         $('#b_button').addClass('hide-all');
         $('#b_icons').addClass('hide-all');
         $('#b_image').addClass('hide-all');
         $('#b_label').addClass('hide-all');

         $('#element-helper section').addClass('disabled').removeClass('active');
         $('#element-helper section .details').removeAttr('style');
         $ehbt.val('');
         $ehbt.attr('disabled', true);

         $('#element-helper').css('pointer-events', 'auto');

         switch (wftype) {
            case 'section':
               plugin._setBackground(props.classes);
               plugin._setMargin(props.classes);
               plugin._setPadding(props.classes);
               plugin._setPosition(props.classes);
               plugin._setAdvanced(props.classes, 'container', props.attr);
               $('#element-helper section.is_section').removeClass('disabled');
               break;

            case 'wcontainer':
               break;

            case 'row':
               plugin._setRows(props.classes, props.attr['wf-label'].nodeValue);
               plugin._setAdvanced(props.classes, 'row', props.attr);
               $('#element-helper section.is_rows').removeClass('disabled');

               $('#element-helper section[data-helper=\'rows\']').addClass('active');
               $('#element-helper section[data-helper=\'rows\'] .details').slideDown(50);
               break;

            case 'columns':
               plugin._setColumns(props.classes);
               plugin._setAdvanced(props.classes, 'columns', props.attr);
               $('#element-helper section.is_columns').removeClass('disabled');
               $('#element-helper section[data-helper=\'columns\']').addClass('active');
               $('#element-helper section[data-helper=\'columns\'] .details').slideDown(50);
               break;

            case 'container':
               plugin._setAdvanced(props.classes, 'container', props.attr);
               plugin._setText(props.classes);
               plugin._setMargin(props.classes);
               plugin._setPadding(props.classes);
               plugin._setDisplay(props.classes);
               plugin._setPosition(props.classes);
               plugin._setBorder(props.classes);
               plugin._setBackground(props.classes);
               $('#element-helper section.is_container').removeClass('disabled');
               break;

            case 'text':
               plugin._setBasic(props.html, props.attr);
               plugin._setPosition(props.classes);
               plugin._setDisplay(props.classes);
               $('#element-helper section.is_text').removeClass('disabled');
               $ehbt.attr('disabled', false);
               $b_text.removeClass('hide-all');
               $ehbd.slideDown(50);
               break;

            case 'img':
               plugin._setBasic(props.html, props.attr);
               plugin._setImage(props.classes);
               $('#element-helper section.is_image').removeClass('disabled');
               $ehbd.slideDown(50);
               break;

            case 'video':
            case 'map':
            case 'audio':
               plugin._setBasic(props.html, props.attr);
               plugin._setFrame();
               $('#element-helper section.is_frame').removeClass('disabled');
               $ehbd.slideDown(50);
               break;

            case 'link':
               plugin._setBasic(props.html, props.attr);
               plugin._setLinks(props.html, props.classes, props.attr, props.element);
               $('#element-helper section.is_link').removeClass('disabled');
               $ehbd.slideDown(50);
               break;

            case 'icon':
               plugin._setBasic(props.html, props.attr);
               plugin._setIcons(props.classes);
               $('#element-helper section.is_icon').removeClass('disabled');
               $ehbd.slideDown(50);
               break;

            case 'label':
               plugin._setBasic(props.html, props.attr);
               plugin._setLabels(props.classes);
               $ehbt.attr('disabled', false);
               $('#element-helper section.is_label').removeClass('disabled');
               $ehbd.slideDown(50);
               break;

            default:
               break;
         }
      },

      /**
       * Description
       * @method _insertPrepare
       * @private
       * @param section
       * @param column
       */
      _insertPrepare: function (section, column) {
         let $sh = $('#section-helper');
         $('#element-helper section').addClass('disabled');

         $sh.find('.wojo.tabs li').removeClass('active').addClass('hidden');
         $sh.find('.wojo.tab > .item').removeClass('active');

         if (section) {
            $sh.find('.wojo.tabs .tab_rows').removeClass('hidden').addClass('active');
            $sh.find('.wojo.tabs .tab_sections').removeClass('hidden');
            $sh.find('#tab_rows').addClass('active');
            insertType = 'section';
         }

         if (column) {
            $sh.find('.wojo.tabs .tab_blocks').removeClass('hidden').addClass('active');
            $sh.find('.wojo.tabs .tab_plugins').removeClass('hidden');
            $sh.find('.wojo.tabs .tab_modules').removeClass('hidden');
            $sh.find('#tab_blocks').addClass('active');
            insertType = 'column';
         }

         $sh.fadeIn(200);

         $sh.on('click', 'a.close', function () {
            $sh.fadeOut(150);
         });
      },

      /**
       * Description
       * @method _insert
       * @return
       */
      _insert: function () {
         let plugin = this;
         $('#section-helper').on('click', '.content a', function () {
            switch ($(this).data('element')) {
               case 'modules':
                  plugin._insertModule($(this).data());
                  break;

               case 'uplugins':
                  plugin._insertUserPlugin($(this).data());
                  break;

               case 'plugins':
                  plugin._insertPlugin($(this).data());
                  break;

               case 'blocks':
                  plugin._insertBlock($(this).data());
                  break;

               case 'rows':
                  plugin.makeRows($(this).data('row'));
                  $('#section-helper').transition('scaleOut', {
                     duration: 200,
                     complete: function () {
                        $(this).hide();
                     }
                  });
                  break;

               default:
                  plugin._insertSection($(this).data());
                  break;
            }
         });
      },

      /**
       * Description
       * @method _insertBlock
       * @param data
       * @private
       */
      _insertBlock: function (data) {
         let plugin = this;
         const url = $.url(plugin.options.burl).segment(-1);

         $.get(plugin.options.aurl + 'builder/action/', {
            action: 'block',
            file: url + '/' + data.html
         }).done(function (json) {
            let jsonObj = JSON.parse(json);
            if (jsonObj.status === 'success') {
               let block = $(jsonObj.html);

               $activeColumn.append(block[0].outerHTML);
               $activeColumn.removeClass('is_empty');
               plugin.prepareColumn($activeColumn);

               $('#section-helper').transition('scaleOut', {
                  duration: 200,
                  complete: function () {
                     $(this).hide();
                  }
               });
            } else {
               console.log('invalid block');
            }
         }, 'json');
      },

      /**
       * Description
       * @method _insertSection
       * @param data
       * @private
       */
      _insertSection: function (data) {
         let plugin = this;
         const url = $.url(plugin.options.burl).segment(-1);

         $.get(plugin.options.aurl + 'builder/action/', {
            action: 'section',
            file: url + '/' + data.html
         }).done(function (json) {
            let jsonObj = JSON.parse(json);
            if (jsonObj.status === 'success') {
               let section = $(jsonObj.html).filter('div.section');
               section.addClass('loading');
               $(section[0].outerHTML).insertAfter($activeSection).attr('temp-id', 'temp_s00001');
               let $new = $('[temp-id=\'temp_s00001\']', plugin.element);
               plugin.prepareSection();

               $('#section-helper').transition('scaleOut', {
                  duration: 200,
                  complete: function () {
                     $(this).hide();
                  }
               });
               $new.removeClass('loading').removeAttr('temp-id');
            } else {
               console.log('invalid block');
            }
         }, 'json');
      },

      /**
       * Description
       * @method _insertPlugin
       * @property {string} pluginId
       * @property {string} pluginAlias
       * @property {string} pluginPlugin_id
       * @param {object} data
       * @return
       */
      _insertPlugin: function (data) {
         let plugin = this;

         $.get(plugin.options.aurl + 'builder/action/', {
            action: 'plugin',
            id: data.pluginId,
            string: '%%' + data.pluginAlias + '|plugin|' + data.pluginPlugin_id + '|' + data.pluginId + '%%'
         }).done(function (json) {
            let jsonObj = JSON.parse(json);
            if (jsonObj.status === 'success') {
               $activeColumn.html('<div data-mode="readonly" wf-type="plugin" wf-label="Plugin" data-wplugin-alias="' + data.pluginAlias + '" ' +
                 'data-wplugin-id="' + data.pluginId + '" data-wplugin-plugin_id="' + data.pluginPlugin_id + '">' + jsonObj.html + '</div>');
               $activeColumn.removeClass('is_empty');
               plugin.prepareColumn($activeColumn);

               $('#section-helper').transition('scaleOut', {
                  duration: 200,
                  complete: function () {
                     $(this).hide();
                  }
               });
               $('#tab_plugins [data-plugin-id=\'' + data.pluginId + '\']').closest('.columns').remove();
            } else {
               console.log('invalid plugin');
            }

         }, 'json');
      },

      /**
       * Description
       * @method _insertUserPlugin
       * @property {string} pluginId
       * @param data
       * @return
       */
      _insertUserPlugin: function (data) {
         let plugin = this;
         $.get(plugin.options.aurl + 'builder/action/', {
            action: 'userPlugin',
            id: data.pluginId,
         }).done(function (json) {
            let jsonObj = JSON.parse(json);
            if (jsonObj.status === 'success') {
               $activeColumn.html('<div data-mode="readonly" wf-type="plugin" wf-label="User Plugin" data-wuplugin-id="' + data.pluginId + '">' + jsonObj.html + '</div>');
               $activeColumn.removeClass('is_empty');
               plugin.prepareColumn($activeColumn);

               $('#section-helper').transition('scaleOut', {
                  duration: 200,
                  complete: function () {
                     $(this).hide();
                  }
               });
            } else {
               console.log('invalid user plugin');
            }
         }, 'json');
      },

      /**
       * Description
       * @method _insertModule
       * @property {string} moduleModule_id
       * @property {string} moduleAlias
       * @property {string} moduleGroup
       * @property {string} moduleId
       * @return
       */
      _insertModule: function (data) {
         let plugin = this;
         $.get(plugin.options.aurl + 'builder/action/', {
            action: 'module',
            id: data.moduleModule_id,
         }).done(function (json) {
            let jsonObj = JSON.parse(json);
            if (jsonObj.status === 'success') {
               $activeColumn.html('<div data-mode="readonly" wf-type="module" wf-label="Module" data-wmodule-alias="' + data.moduleAlias + '" data-wmodule-module_id="' + data.moduleModule_id + '" ' + 'data-wmodule-id="' + data.moduleId + '">' + jsonObj.html + '</div>');

               $activeColumn.removeClass('is_empty');
               plugin.prepareColumn($activeColumn);

               $('#section-helper').transition('scaleOut', {
                  duration: 200,
                  complete: function () {
                     $(this).hide();
                  }
               });
               $('#tab_modules [data-module-group=\'' + data.moduleGroup + '\']').closest('.columns').remove();
            } else {
               console.log('invalid module');
            }

         }, 'json');
      },

      /**
       * Description
       * @method _setBasic
       * @param html
       * @param name
       * @private
       */
      _setBasic: function (html, name) {
         const $element = $('#element-helper [data-helper=basic]');
         $element.find('input[name=basic_name]').val(name['wf-label'].nodeValue);
         $element.find('textarea[name=basic_text]').val(html);
      },

      /**
       * Description
       * @method _setPadding
       * @param classes
       * @private
       */
      _setPadding: function (classes) {
         const $element = $('#paddings_container');
         let template;

         $element.html('');

         let result = classes.filter(function (item) {
            return typeof item == 'string' && item.indexOf('padding') > -1;
         });

         if (result.length > 0) {
            let items;
            let iconDevice;
            let sizeText;
            let iconType;

            $.each(result, function (index, value) {
               items = value.split('-');
               switch (items.length) {
                  case 4:
                     iconDevice = items[3];
                     iconType = 'icon wysiwyg border ' + items[2];
                     sizeText = items[1];
                     break;
                  case 3:
                     if (items[2] === 'phone' || items[2] === 'mobile' || items[2] === 'tablet') {
                        iconDevice = items[2];
                        iconType = 'icon wysiwyg border ' + items[1];
                        sizeText = 'default';
                     } else {
                        iconDevice = 'screen';
                        iconType = 'icon wysiwyg border ' + items[2];
                        sizeText = items[1];
                     }
                     break;
                  case 2:
                     if (items[1] === 'phone' || items[1] === 'mobile' || items[1] === 'tablet') {
                        iconDevice = items[1];
                        iconType = 'icon wysiwyg border outer';
                        sizeText = 'default';
                     } else {
                        iconDevice = 'screen';
                        iconType = 'icon wysiwyg border outer';
                        sizeText = items[1];
                     }
                     break;
                  default:
                     iconDevice = 'screen';
                     iconType = 'icon wysiwyg border outer';
                     sizeText = 'default';
                     break;
               }

               switch (sizeText) {
                  case 'huge':
                     sizeText = '96px';
                     break;
                  case 'big':
                     sizeText = '80px';
                     break;
                  case 'large':
                     sizeText = '64px';
                     break;
                  case 'medium':
                     sizeText = '48px';
                     break;
                  case 'small':
                     sizeText = '16px';
                     break;
                  case 'mini':
                     sizeText = '8px';
                     break;
                  default:
                     sizeText = '32px';
                     break;
               }

               template = ''
                 + '<div class="wojo wojo mini buttons" data-value="' + value + '">'
                 + '<a class="wojo mini secondary inverted button passive start">'
                 + '<i class="icon ' + iconDevice + '"></i>' + sizeText + ' </a>'
                 + '<a class="wojo icon mini secondary inverted passive button auto">'
                 + '<i class="' + iconType + '"></i>'
                 + '</a>'
                 + '<a data-class="' + value + '" class="wojo icon mini primary inverted button auto removePadding">'
                 + '<i class="icon x alt"></i>'
                 + '</a>'
                 + '</div>';
               $element.append(template);
            });

         }
      },

      /**
       * Description
       * @method _setMargin
       * @param classes
       * @private
       */
      _setMargin: function (classes) {
         const $element = $('#margins_container');
         let template;

         $element.html('');

         let result = classes.filter(function (item) {
            return typeof item == 'string' && item.indexOf('margin') > -1;
         });

         if (result.length > 0) {
            let items;
            let iconDevice;
            let sizeText;
            let iconType;

            $.each(result, function (index, value) {
               items = value.split('-');
               switch (items.length) {
                  case 4:
                     iconDevice = items[3];
                     iconType = 'icon wysiwyg border ' + items[2];
                     sizeText = items[1];
                     break;
                  case 3:
                     if (items[2] === 'phone' || items[2] === 'mobile' || items[2] === 'tablet') {
                        iconDevice = items[2];
                        iconType = 'icon wysiwyg border ' + items[1];
                        sizeText = 'default';
                     } else {
                        iconDevice = 'screen';
                        iconType = 'icon wysiwyg border ' + items[2];
                        sizeText = items[1];
                     }
                     break;
                  case 2:
                     if (items[1] === 'phone' || items[1] === 'mobile' || items[1] === 'tablet') {
                        iconDevice = items[1];
                        iconType = 'icon wysiwyg border outer';
                        sizeText = 'default';
                     } else {
                        iconDevice = 'screen';
                        iconType = 'icon wysiwyg border outer';
                        sizeText = items[1];
                     }
                     break;
                  default:
                     iconDevice = 'screen';
                     iconType = 'icon wysiwyg border outer';
                     sizeText = 'default';
                     break;
               }

               switch (sizeText) {
                  case 'huge':
                     sizeText = '96px';
                     break;
                  case 'big':
                     sizeText = '80px';
                     break;
                  case 'large':
                     sizeText = '64px';
                     break;
                  case 'medium':
                     sizeText = '48px';
                     break;
                  case 'small':
                     sizeText = '16px';
                     break;
                  case 'mini':
                     sizeText = '8px';
                     break;
                  default:
                     sizeText = '32px';
                     break;
               }

               template = ''
                 + '<div class="wojo wojo mini buttons" data-value="' + value + '">'
                 + '<a class="wojo mini secondary inverted button passive start">'
                 + '<i class="icon ' + iconDevice + '"></i>' + sizeText + ' </a>'
                 + '<a class="wojo icon mini secondary inverted passive button auto">'
                 + '<i class="' + iconType + '"></i>'
                 + '</a>'
                 + '<a data-class="' + value + '" class="wojo icon mini primary inverted button auto removeMargin">'
                 + '<i class="icon x alt"></i>'
                 + '</a>'
                 + '</div>';
               $element.append(template);
            });
         }
      },

      /**
       * Description
       * @method _setText
       * @param classes
       * @private
       */
      _setText: function (classes) {
         const $align = $('#text_align');
         const $weight = $('#text_weight');
         const $color = $('#text_color');
         const $size = $('#text_size');
         const $decoration = $('#text_decoration');

         $align.find('.button').removeClass('active');
         $decoration.find('.button').removeClass('active');
         $size.find('.button').removeClass('active');
         $weight.find('.button').removeClass('active');
         $color.find('.button').removeClass('active');

         $.each(classes, function (index, item) {
            $align.find('.button[data-class=\'' + item + '\']').addClass('active');
            $decoration.find('.button[data-class=\'' + item + '\']').addClass('active');
            $size.find('.button[data-class=\'' + item + '\']').addClass('active');
            $weight.find('.button[data-class=\'' + item + '\']').addClass('active');
            $color.find('.button[data-text=\'' + item + '\']').addClass('active');
         });
      },

      /**
       * Description
       * @method _setRows
       * @param classes
       * @param name
       * @private
       */
      _setRows: function (classes, name) {
         let plugin = this;

         const $gutters = $('#rows_gutters');
         const $align = $('#rows_align');
         const $justify = $('#rows_justify');

         let gutters = 'none';
         let align = 'none';
         let justify = 'none';

         const $element = $('#element-helper [data-helper=basic]');
         $element.find('input[name=basic_name]').val(name);

         let exclude = ['row', 'active', 'live'];
         let filtered = classes.filter(function (item) {
            return !exclude.includes(item);
         });

         $('section[data-helper=\'rows\']  .button').removeClass('active');

         $.each(filtered, function (index, item) {
            if (item.toLowerCase().indexOf('gutters') !== -1) {
               gutters = item;
            }
            if (item.toLowerCase().indexOf('justify') !== -1) {
               justify = item;
            }
            if (item.toLowerCase().indexOf('align') !== -1) {
               align = item;
            }
         });
         $gutters.find('.button[data-class=\'' + gutters + '\']').addClass('active');
         $align.find('.button[data-class=\'' + align + '\']').addClass('active');
         $justify.find('.button[data-class=\'' + justify + '\']').addClass('active');

         //set visibility
         plugin._setVisibility(filtered);
      },

      /**
       * Description
       * @method _setColumns
       * @param classes
       * @private
       */
      _setColumns: function (classes) {
         let plugin = this;

         const $size = $('#columns_size');
         const $order = $('#columns_order');

         let screen, tablet, mobile, phone;

         $('section[data-helper=\'columns\']  select').val('none');

         if ($.inArray('auto', classes) !== -1) {
            $size.find('.button').addClass('active');
         }

         let exclude = ['row', 'columns', 'live'];
         let filtered = classes.filter(function (item) {
            return !exclude.includes(item);
         });

         screen = filtered.filter(function (item) {
            return typeof item == 'string' && item.indexOf('screen-') > -1;
         });
         tablet = filtered.filter(function (item) {
            return typeof item == 'string' && item.indexOf('tablet-') > -1;
         });
         mobile = filtered.filter(function (item) {
            return typeof item == 'string' && item.indexOf('mobile-') > -1;
         });
         phone = filtered.filter(function (item) {
            return typeof item == 'string' && item.indexOf('phone-') > -1;
         });

         if (screen.length > 0) {
            let items;
            let screenOrder;
            let screenSize;
            $.each(screen, function (index, value) {
               items = value.split('-');
               switch (items.length) {
                  case 3:
                     screenOrder = items[2];
                     break;

                  case 2:
                     screenSize = items[1];
                     break;

                  default:
                     screenSize = 'none';
                     screenOrder = 'none';
                     break;

               }
            });
            $size.find('select[name=screen]').val(screenSize);
            $order.find('select[name=screen]').val(screenOrder);
         }

         if (tablet.length > 0) {
            let items;
            let tabletOrder;
            let tabletSize;
            $.each(tablet, function (index, value) {
               items = value.split('-');
               switch (items.length) {
                  case 3:
                     tabletOrder = items[2];
                     break;

                  case 2:
                     tabletSize = items[1];
                     break;

                  default:
                     tabletSize = 'none';
                     tabletOrder = 'none';
                     break;

               }
            });
            $size.find('select[name=tablet]').val(tabletSize);
            $order.find('select[name=tablet]').val(tabletOrder);
         }

         if (mobile.length > 0) {
            let items;
            let mobileOrder;
            let mobileSize;
            $.each(mobile, function (index, value) {
               items = value.split('-');
               switch (items.length) {
                  case 3:
                     mobileOrder = items[2];
                     break;

                  case 2:
                     mobileSize = items[1];
                     break;

                  default:
                     mobileSize = 'none';
                     mobileOrder = 'none';
                     break;

               }
            });
            $size.find('select[name=mobile]').val(mobileSize);
            $order.find('select[name=mobile]').val(mobileOrder);
         }

         if (phone.length > 0) {
            let items;
            let phoneOrder;
            let phoneSize;
            $.each(phone, function (index, value) {
               items = value.split('-');
               switch (items.length) {
                  case 3:
                     phoneOrder = items[2];
                     break;

                  case 2:
                     phoneSize = items[1];
                     break;

                  default:
                     phoneSize = 'none';
                     phoneOrder = 'none';
                     break;

               }
            });
            $size.find('select[name=phone]').val(phoneSize);
            $order.find('select[name=phone]').val(phoneOrder);
         }

         //set visibility
         plugin._setVisibility(filtered);
      },

      /**
       * Description
       * @method _setDisplay
       * @param classes
       * @private
       */
      _setDisplay: function (classes) {
         const $type = $('#display_type');
         $type.find('.button').removeClass('active');

         const arr = ['display-inline', 'display-inline-block', 'display-block', 'display-flex', 'display-inline-flex'];
         for (let i = 0; i < arr.length; i++) {
            if ($.inArray(arr[i], classes) > -1) {
               $type.find('.button[data-class=' + arr[i] + ']').addClass('active');
            }
         }
      },

      /**
       * Description
       * @method _setPosition
       * @param classes
       * @private
       */
      _setPosition: function (classes) {
         const $type = $('#position_type');
         const $index = $('#position_index');
         const $place = $('#position_place');

         const $pi = $('#p_index');
         const $pp = $('#p_place');

         $index.find('.button').removeClass('active');
         $place.find('.button').removeClass('active');
         $type.find('.button').removeClass('active');

         // set index
         let zindex = classes.filter(function (item) {
            return typeof item == 'string' && item.indexOf('zindex') > -1;
         });

         if (zindex.length > 0) {
            $index.find('.button[data-class=' + zindex[0] + ']').addClass('active');
            $pi.removeClass('hide-all');
         }

         // set position
         const arr = ['static', 'relative', 'absolute', 'fixed'];
         for (let i = 0; i < arr.length; i++) {
            if ($.inArray(arr[i], classes) > -1) {
               $type.find('.button[data-class=' + arr[i] + ']').addClass('active');
            }
         }

         // set placement
         let result = classes.filter(function (item) {
            return typeof item == 'string' && item.indexOf('position') > -1;
         });

         if (result.length > 0) {
            $pp.removeClass('hide-all');
            let items;
            let position;
            let iconType;

            $.each(result, function (index, value) {
               items = value.split('-');
               switch (items.length) {
                  case 3:
                     iconType = items[1];
                     position = items[2];
                     break;

                  case 2:
                     iconType = items[1];
                     position = 'zero';
                     break;
                  default:
                     break;

               }
               $place.find('.button[data-class=' + iconType + '-' + position + ']').addClass('active');
            });
         }
      },

      /**
       * Description
       * @method _setBorder
       * @param classes
       * @private
       */
      _setBorder: function (classes) {
         const $type = $('#border_type');
         const $size = $('#border_size');
         const $radius = $('#border_radius');
         const $color = $('#border_color');

         $type.find('.button').removeClass('active');
         $size.find('.button').removeClass('active');
         $radius.find('.button').removeClass('active');
         $color.find('.button').removeClass('active');

         let rounded = classes.filter(function (item) {
            return typeof item == 'string' && item.indexOf('rounded') > -1;
         });

         if (rounded.length > 0) {
            let radius;
            let ritems = rounded[0].split('-');
            if (ritems[1] !== null && ritems[1] !== undefined) {
               radius = 'rounded-' + ritems[1];
            } else {
               radius = 'rounded-full';
            }
            $radius.find('.button[data-class=' + radius + ']').addClass('active');
         }

         let border = classes.filter(function (item) {
            return typeof item == 'string' && item.indexOf('border') > -1;
         });

         if (border.length > 0) {
            let items;
            let color;
            let size;
            let type;
            $.each(border, function (index, value) {
               items = value.split('-');
               switch (items[1]) {
                  case 'color':
                     color = value;
                     break

                  case '5':
                  case '4':
                  case '3':
                  case '2':
                  case '1':
                     size = 'border-' + items[1];
                     break;

                  case 'top':
                  case 'bottom':
                  case 'left':
                  case 'right':
                     type = 'border-' + items[1];
                     break;

                  default:
                     type = 'border-full';
                     break;

               }
            });

            $type.find('.button[data-class=' + type + ']').addClass('active');
            $size.find('.button[data-class=' + size + ']').addClass('active');
            $color.find('.button[data-border=' + color + ']').addClass('active');
         }
      },

      /**
       * Description
       * @method _setVisibility
       * @param classes
       * @private
       */
      _setVisibility: function (classes) {
         const $opacity = $('#visibility_opacity');
         const $overflow = $('#visibility_overflow');
         const $visibility = $('#visibility_visibility');

         $visibility.find('.button').removeClass('active');
         $opacity.val('none').attr('selected', true);
         $overflow.find('.button').removeClass('active');

         let opacity = 'none';
         let overflow = 'none';

         $.each(classes, function (index, item) {
            if (item.toLowerCase().indexOf('opacity') !== -1) {
               opacity = item;
            }
            if (item.toLowerCase().indexOf('overflow') !== -1) {
               overflow = item;
            }
            $visibility.find('.button[data-class=\'' + item + '\']').addClass('active');
         });
         $opacity.val(opacity).attr('selected', true);
         $overflow.find('.button[data-class=\'' + overflow + '\']').addClass('active');
      },

      /**
       * Description
       * @method _setBackground
       * @param classes
       * @private
       */
      _setBackground: function (classes) {
         let plugin = this;

         const $color = $('#background_color');
         const $position = $('#background_position');
         const $size = $('#background_size');
         const $fixed = $('#background_fixed');
         const $repeat = $('#background_repeat');
         const $image = $('#bgImageHolder');
         const attr = $activeElement.attr('data-image');

         $color.find('.button').removeClass('active');
         $position.find('.button').removeClass('active');
         $size.find('.button').removeClass('active');

         let color = classes.filter(function (item) {
            return typeof item == 'string' && item.indexOf('border-color-') > -1;
         });

         if (color.length > 0) {
            $color.find('.button[data-bg=' + color + ']').addClass('active');
         }

         if (typeof attr !== 'undefined' && attr !== false) {
            let position = classes.filter(function (item) {
               return typeof item == 'string' && item.indexOf('bg-position') > -1;
            });

            if (position.length > 0) {
               $position.find('.button[data-class=' + position + ']').addClass('active');
            }

            let size = classes.filter(function (item) {
               return typeof item == 'string' && item.indexOf('bg-size') > -1;
            });

            if (size.length > 0) {
               $size.find('.button[data-class=' + size + ']').addClass('active');
            }

            if ($.inArray('bg-repeat-none', classes) !== -1) {
               $repeat.prop('checked', true);
            } else {
               $repeat.prop('checked', false);
            }

            if ($.inArray('bg-fixed', classes) !== -1) {
               $fixed.prop('checked', true);
            } else {
               $fixed.prop('checked', false);
            }

            $image.html('<img src="' + plugin.options.upurl + '/' + attr + '" alt="">');
         }
      },

      /**
       * Description
       * @method _setAdvanced
       * @param classes
       * @param type
       * @param attr
       * @private
       */
      _setAdvanced: function (classes, type, attr) {
         let exclude = ['row', 'columns', 'active', 'live'];
         let filtered = classes.filter(function (item) {
            return !exclude.includes(item);
         });

         let newList = '';
         $.each(filtered, function (index, value) {
            newList += '<div class="wojo mini right dark inverted label" data-value="' + value + '">'
              + '' + value + '<a class="inline-flex"><i class="icon x negative alt"></i></a></div> ';
         });

         $('#advanced_classes').html(newList);
         $('input[name=attributes_value]').val('');

         if (typeof attr['id'] !== 'undefined') {
            $('#advanced_id').html('<div class="wojo mini right dark inverted label" data-value="' + attr['id'].nodeValue + '">'
              + '' + attr['id'].nodeValue + '<a class="inline-flex"><i class="icon x negative alt"></i></a></div>');
         } else {
            $('#advanced_id').html('');
         }
         const $htmlButton = $('#advanced_html .button');
         (type === 'container') ? $htmlButton.removeClass('disabled') : $htmlButton.addClass('disabled');
      },

      /**
       * Description
       * @method _setLinks
       * @param classes
       * @param html
       * @param attr
       * @param element
       * @private
       */
      _setLinks: function (html, classes, attr, element) {
         let plugin = this;
         const $bt = $('#b_text');
         const $bl = $('#b_link');
         const $bcontainer = $('#b_button');
         const $links = $('#basic_links');
         const $urlText = $('#basic_url_text');
         const $burlText = $('#b_url_text');
         const $url = $('#basic_url');
         let link = (attr.href) ? attr.href.nodeValue : '#!';

         $bt.addClass('hide-all');

         if (typeof element !== 'undefined') {
            if (element.children().length) {
               $urlText.attr('disabled', true);
            } else {
               $urlText.val(plugin.cleanText(html));
               $urlText.attr('disabled', false);
            }
         }

         let button = classes.filter(function (item) {
            return typeof item == 'string' && item.indexOf('button') > -1;
         });

         if (button.length > 0) {
            plugin._setButton(classes);
            plugin._buttonSection();
            $bcontainer.removeClass('hide-all');
         }

         if (html.match(/<img/)) {
            $burlText.addClass('hide-all');
         } else {
            $burlText.removeClass('hide-all');
         }

         this._linkList($links, link);

         $url.val(link)
         $links.find('option[value=\'' + link + '\']').prop('selected', true);
         $bl.removeClass('hide-all');
      },

      /**
       * Description
       * @method _setIcons
       * @param classes
       * @private
       */
      _setIcons: function (classes) {
         const $bicon = $('#b_icons');
         const $icontainer = $('#icon_list');
         const $bcontainer = $('#b_button');
         const $icolor = $('#icon_color');

         let color, icolor, inverted = false;

         $bcontainer.find('.button').removeClass('active');
         $icolor.find('.button').removeClass('active');
         $icontainer.find('.button').removeClass('active');

         let excludeColor = ['icon', 'active', 'live', 'inverted', 'primary', 'secondary', 'positive', 'negative', 'alert', 'info', 'light', 'dark', 'white'];
         let basic = classes.filter(function (item) {
            return !excludeColor.includes(item);
         });

         let includeColor = ['inverted', 'primary', 'secondary', 'positive', 'negative', 'alert', 'info', 'light', 'dark', 'white'];
         let colors = classes.filter(function (item) {
            return includeColor.includes(item);
         });

         $.each(colors, function (index, item) {
            switch (item) {
               case 'primary':
               case 'secondary':
               case 'positive':
               case 'negative':
               case 'alert':
               case 'info':
               case 'light':
               case 'dark':
               case 'white':
                  icolor = item;
                  break;

               case 'inverted':
                  inverted = true;
                  break;

               default:
                  break;
            }
         });

         if (inverted) {
            color = icolor + '-inverted';
         } else {
            color = icolor;
         }

         $icolor.find('.button[data-class=\'' + color + '\']').addClass('active')
         $icontainer.find('.button[data-class=\'' + basic.join(' ') + '\']').addClass('active');
         $bicon.removeClass('hide-all');
      },

      /**
       * Description
       * @method _setLabels
       * @param classes
       * @private
       */
      _setLabels: function (classes) {
         const $bt = $('#b_text');
         const $blabel = $('#b_label');
         const $bsize = $('#label_size');
         const $bcolor = $('#label_color');

         let color, bcolor, binverted = false;

         $blabel.find('.button').removeClass('active');
         $bcolor.find('.button').removeClass('active');

         let exclude = ['wojo', 'active', 'live'];
         let filtered = classes.filter(function (item) {
            return !exclude.includes(item);
         });

         $.each(filtered, function (index, item) {
            switch (item) {
               case 'mini':
               case 'small':
               case 'big':
                  $bsize.find('.button[data-class=' + item + ']').addClass('active');
                  $bsize.find('.button[data-class=\'default\']').removeClass('active');
                  break;

               case 'primary':
               case 'secondary':
               case 'positive':
               case 'negative':
               case 'alert':
               case 'info':
               case 'light':
               case 'dark':
               case 'white':
               case 'grey':
                  bcolor = item;
                  break;

               case 'inverted':
                  binverted = true
                  break;

               default:
                  $bsize.find('.button[data-class=\'default\']').addClass('active');
                  break;
            }
         });

         if (binverted) {
            color = bcolor + '-inverted';
         } else {
            color = bcolor;
         }

         $bcolor.find('.button').removeClass('active');
         $bcolor.find('.button[data-class=\'' + color + '\']').addClass('active')
         $bt.removeClass('hide-all');
         $blabel.removeClass('hide-all');
      },

      /**
       * Description
       * @method _setImage
       * @param classes
       * @private
       */
      _setImage: function (classes) {
         const $image = $('#imageHolder');
         const $alt = $('#basic_image_alt');
         const $istyle = $('#image_style');
         const $bimg = $('#b_image');

         let image = $activeElement.attr('src');
         let alt = $activeElement.attr('alt');

         $istyle.find('.button').removeClass('active');

         let exclude = ['wojo', 'active', 'image', 'live'];
         let filtered = classes.filter(function (item) {
            return !exclude.includes(item);
         });

         $.each(filtered, function (index, item) {
            switch (item) {
               case 'rounded':
               case 'circular':
                  $istyle.find('.button[data-class=' + item + ']').addClass('active');
                  $istyle.find('.button[data-class=\'default\']').removeClass('active');
                  break;

               default:
                  $istyle.find('.button[data-class=\'default\']').addClass('active');
                  break;
            }
         });

         $alt.val(alt);
         $image.html('<img src="' + image + '" alt="">');
         $bimg.removeClass('hide-all');
      },

      /**
       * Description
       * @method _setFrame
       * @private
       */
      _setFrame: function () {
         const $element = $('#element-helper [data-helper=basic]');
         const $fl = $('#frame_link');

         let url = $activeElement.children('iframe').attr('src');
         $element.find('input[name=frame_url]').val(url);
         $fl.show()
      },

      /**
       * Description
       * @method _setButton
       * @param classes
       * @private
       */
      _setButton: function (classes) {
         const $bcontainer = $('#b_button');
         const $icontainer = $('#icon_list');
         const $bsize = $('#button_size');
         const $bstyle = $('#button_style');
         const $bwidth = $('#button_width');
         const $bpos = $('#button_position');
         const $bcolor = $('#button_color');

         let color, bcolor, binverted = false;

         $bcontainer.find('.button').removeClass('active');
         $icontainer.find('.button').removeClass('active');
         $bcolor.find('.button').removeClass('active');
         $bpos.find('.button').removeClass('active');

         let exclude = ['wojo', 'active', 'live'];
         let filtered = classes.filter(function (item) {
            return !exclude.includes(item);
         });

         $.each(filtered, function (index, item) {
            switch (item) {
               case 'mini':
               case 'small':
               case 'big':
                  $bsize.find('.button[data-class=' + item + ']').addClass('active');
                  $bsize.find('.button[data-class=\'default\']').removeClass('active');
                  break;

               case 'rounded':
               case 'circular':
               case 'icon':
                  $bstyle.find('.button[data-class=' + item + ']').addClass('active');
                  $bstyle.find('.button[data-class=\'default\']').removeClass('active');
                  break;

               case 'fluid':
                  $bwidth.find('.button[data-class=' + item + ']').addClass('active');
                  $bwidth.find('.button[data-class=\'auto\']').removeClass('active');
                  break;

               case 'right':
                  $bpos.find('.button[data-class=right]').addClass('active');
                  $bpos.find('.button[data-class=\'default\']').removeClass('active');
                  break;

               case 'primary':
               case 'secondary':
               case 'positive':
               case 'negative':
               case 'alert':
               case 'info':
               case 'light':
               case 'dark':
               case 'white':
               case 'grey':
                  bcolor = item;
                  break;

               case 'inverted':
                  binverted = true
                  break;

               default:
                  $bsize.find('.button[data-class=\'default\']').addClass('active');
                  $bstyle.find('.button[data-class=\'default\']').addClass('active');
                  $bwidth.find('.button[data-class=\'auto\']').addClass('active');
                  $bpos.find('.button[data-class=\'default\']').addClass('active');
                  break;
            }
         });

         if (binverted) {
            color = bcolor + '-inverted';
         } else {
            color = bcolor;
         }

         $bcolor.find('.button').removeClass('active');
         $bcolor.find('.button[data-class=\'' + color + '\']').addClass('active')

         if ($activeElement !== null && $activeElement !== undefined) {
            let $icon = $activeElement.children('.icon');
            if ($icon.length > 0) {
               let icon = $icon.attr('class');
               icon = icon.replace('icon ', '');
               $icontainer.find('.button[data-class=\'' + icon + '\']').addClass('active');
            }
         }
      },

      /**
       * Description
       * @method _linkSection
       * @return
       */
      _linkSection: function () {
         let plugin = this;
         const $links = $('#basic_links');
         const $url = $('#basic_url');
         const $urlText = $('#basic_url_text');
         const $remLink = $('#removeLink');

         let timeout;
         const delay = 1200;

         $urlText.on('keyup', function () {
            if ($activeElement !== null && $activeElement !== undefined) {
               let _this = $(this);
               clearTimeout(timeout);
               timeout = setTimeout(function () {
                  $activeElement.text(plugin.cleanText(_this.val()));
               }, delay);
            }
         });

         $url.on('keyup', function () {
            if ($activeElement !== null && $activeElement !== undefined) {
               let _this = $(this);
               clearTimeout(timeout);
               timeout = setTimeout(function () {
                  $activeElement.attr('href', plugin.cleanText(_this.val()));
               }, delay);
            }
         });

         $links.on('change', function () {
            let active = $(this).val()
            if ($activeElement !== null && $activeElement !== undefined) {
               $activeElement.attr('href', active);
               $url.val(active);
            }
         });

         $remLink.on('click', function () {
            if ($activeElement !== null && $activeElement !== undefined) {
               $activeElement.contents().unwrap();
            }
         });
      },

      /**
       * Description
       * @method _labelSection
       * @return
       */
      _labelSection: function () {
         const $bcontainer = $('#b_label');
         const $size = $('#label_size');
         const $color = $('#label_color');

         let size, color;

         $bcontainer.on('click', '.button', function () {
            if ($activeElement !== null && $activeElement !== undefined) {
               let id = $(this).closest('.buttons').attr('id');
               $('#' + id).find('.button').removeClass('active');

               $(this).addClass('active');

               color = $color.find('.button.active').data('class');
               size = $size.find('.button.active').data('class');
               size = (size === 'default') ? size.replace('default', '') : ' ' + size;

               let bcolor = color.split('-');
               if (bcolor.length === 2) {
                  color = ' ' + bcolor[0] + ' inverted';
               } else {
                  color = ' ' + color;
               }
               let classes = size + color;
               $activeElement.removeClass('mini small big primary secondary positive negative inverted alert info white grey 500 300 light dark');
               $activeElement.addClass(classes);
            }
         });
      },

      /**
       * Description
       * @method _buttonSection
       * @return
       */
      _buttonSection: function () {
         let plugin = this;

         const $bcontainer = $('#b_button');
         const $url = $('#basic_url_text');
         let timeout;
         const delay = 1200;

         $bcontainer.on('click', '.button', function () {
            if ($activeElement !== null && $activeElement !== undefined) {
               let id = $(this).closest('.buttons').attr('id');
               $('#' + id).find('.button').removeClass('active');
               $(this).addClass('active');
               plugin._parseButton();
            }
         });

         $url.on('keyup', function () {
            if ($activeElement !== null && $activeElement !== undefined) {
               clearTimeout(timeout);
               timeout = setTimeout(function () {
                  plugin._parseButton();
               }, delay);
            }
         });
      },

      /**
       * Description
       * @method _imageSection
       * @return
       */
      _imageSection: function () {
         let plugin = this;
         const $image = $('#basic_image');
         const $holder = $('#imageHolder');
         const $style = $('#image_style');

         $style.on('click', '.button', function () {
            let value = $(this).data('class');
            if ($activeElement !== null && $activeElement !== undefined) {
               $style.find('.button').removeClass('active');
               $(this).addClass('active');
               $activeElement.removeClass('rounded circular');
               if (value !== 'default') {
                  $activeElement.addClass(value);
               }

            }
         });

         $image.on('click', function () {
            $.ajax({
               url: plugin.options.url + '/filepicker.php',
               type: 'GET',
               data: {
                  pickFile: 1,
                  editor: true
               },
               async: true
            }).done(function (data) {
               $('<div class="wojo big modal"><div class="dialog" role="document"><div class="content">' + data + '</div></div></div>').modal();
               $('#result').on('click', '.is_file', function () {
                  let dataset = $(this).data('set');
                  if (dataset.image === 'true') {
                     $holder.html('<img src="' + plugin.options.upurl + dataset.url + '" alt="">');
                     if ($activeElement !== null && $activeElement !== undefined) {
                        $activeElement.attr({
                           'src': plugin.options.upurl + dataset.url
                        });
                     }
                     $.modal.close();
                  }
               });
            });
         });
      },

      /**
       * Description
       * @method _frameSection
       * @return
       */
      _frameSection: function () {
         const $url = $('input[name=frame_url]');
         const $button = $('#frame_update');

         $button.on('click', function () {
            let value = $url.val();
            if ($.trim(value).length) {
               if ($activeElement !== null && $activeElement !== undefined) {
                  $activeElement.children('iframe').attr({
                     'src': value
                  });
               }
            }
         });
      },

      /**
       * Description
       * @method _iconSection
       * @return
       */
      _iconSection: function () {
         const $icons = $('#icon_list');
         const $icolor = $('#icon_color');

         $icons.on('click', '.button', function () {
            if ($activeElement !== null && $activeElement !== undefined) {
               let classes = $(this).data('class');
               $icons.find('.button').removeClass('active');
               $(this).addClass('active');

               $activeElement.keepClasses('icon live');
               $activeElement.addClass(classes);
            }
         });

         $icolor.on('click', '.button', function () {
            if ($activeElement !== null && $activeElement !== undefined) {
               let classes = $(this).data('class');
               $icolor.find('.button').removeClass('active');
               $(this).addClass('active');

               $activeElement.removeClass('primary secondary positive negative inverted alert info white grey 500 300 light dark');
               $activeElement.addClass(classes.replace('-', ' '));
            }
         });
      },

      /**
       * Description
       * @method _basicSection
       * @return
       */
      _basicSection: function () {
         let plugin = this;
         const $element = $('#element-helper textarea[name=\'basic_text\']')
         const $basic = $('#basicTextEdit');
         let timeout;
         const delay = 1000;

         $element.on('keyup', function () {
            if ($activeElement !== null && $activeElement !== undefined) {
               let _this = $(this);
               clearTimeout(timeout);
               timeout = setTimeout(function () {
                  //$activeElement.text(plugin.cleanText(_this.val()));
                  $activeElement.html(_this.val());
               }, delay);
            }
         });

         $basic.on('click', '.button', function () {
            let type = $(this).data('type');
            if (typeof $activeElement !== undefined) {
               let selected = plugin.getSelectedText();
               if ($.trim(selected).length !== 0) {
                  let src_str = $activeElement.html();
                  let term = selected;
                  term = term.replace(/(\s+)/, '(<[^>]+>)*$1(<[^>]+>)*');
                  let pattern = new RegExp('(' + term + ')', 'gi');

                  switch (type) {
                     case 'text':
                        src_str = src_str.replace(pattern, '<span>$1</span>');
                        $activeElement.html(src_str);
                        break;
                     default:
                        src_str = src_str.replace(pattern, '<a>$1</a>');
                        $activeElement.html(src_str);
                        break;
                  }
               }
               plugin.prepareSection();
            }
         });
      },

      /**
       * Description
       * @method _paddingSection
       * @return
       */
      _paddingSection: function () {
         const $device = $('#paddings_device');
         const $size = $('#paddings_size');
         const $type = $('#paddings_directions');
         const $paddings = $('#paddings_container');

         let finalClass = '';
         let padding = '';
         let iconType;
         let iconDevice;
         let sizeText;

         $device.on('click', '.button', function () {
            $device.find('.button').removeClass('active');
            $(this).addClass('active');
            iconDevice = $(this).data('type');
         });

         $size.on('click', '.button', function () {
            $size.find('.button').removeClass('active');
            $(this).addClass('active');
            sizeText = $(this).text();
         });

         $type.on('click', '.button', function () {
            $type.find('.button').removeClass('active');
            $(this).addClass('active');
            iconType = $(this).children().attr('class');
         });

         $paddings.on('click', '.removePadding', function () {
            let value = $(this).data('class');
            if ($activeElement !== null && $activeElement !== undefined) {
               $activeElement.removeClass(value);
               $(this).closest('.buttons').remove();
            }
         });

         $('#addPadding').on('click', function () {
            let device = $device.find('.button.active').data('type');
            let type = $type.find('.button.active').data('class');
            let size = $size.find('.button.active').data('class');

            if (device !== undefined && size !== undefined) {
               switch (device) {
                  case 'screen':
                     device = '';
                     break;

                  default:
                     device = '-' + device;
                     break;
               }
               switch (size) {
                  case 'default':
                     size = '';
                     break;

                  default:
                     size = '-' + size;
                     break;
               }

               switch (type) {
                  case 'default':
                     type = '';
                     break;

                  default:
                     type = '-' + type;
                     break;
               }

               finalClass = 'padding' + size + type + device;

               padding = ''
                 + '<div class="wojo wojo mini buttons" data-value="' + finalClass + '">'
                 + '<a class="wojo mini secondary inverted button passive start">'
                 + '<i class="icon ' + iconDevice + '"></i>' + sizeText + ' </a>'
                 + '<a class="wojo icon mini secondary inverted passive button auto">'
                 + '<i class="' + iconType + '"></i>'
                 + '</a>'
                 + '<a data-class="' + finalClass + '" class="wojo icon mini primary inverted button auto removePadding">'
                 + '<i class="icon x alt"></i>'
                 + '</a>'
                 + '</div>';

               let exists = $paddings.children('.buttons[data-value=\'' + finalClass + '\']').length;

               if (!exists && $activeElement !== null && $activeElement !== undefined) {
                  $paddings.append(padding);
                  $activeElement.addClass(finalClass);
               }
            }
         });
      },

      /**
       * Description
       * @method _marginSection
       * @return
       */
      _marginSection: function () {
         const $device = $('#margins_device');
         const $size = $('#margins_size');
         const $type = $('#margins_directions');
         const $margins = $('#margins_container');
         let finalClass = '';
         let margin = '';
         let iconType;
         let iconDevice;
         let sizeText;

         $device.on('click', '.button', function () {
            $device.find('.button').removeClass('active');
            $(this).addClass('active');
            iconDevice = $(this).data('type');
         });

         $size.on('click', '.button', function () {
            $size.find('.button').removeClass('active');
            $(this).addClass('active');
            sizeText = $(this).text();
         });

         $type.on('click', '.button', function () {
            $type.find('.button').removeClass('active');
            $(this).addClass('active');
            iconType = $(this).children().attr('class');
         });

         $margins.on('click', '.removeMargin', function () {
            let value = $(this).data('class');
            if ($activeElement !== null && $activeElement !== undefined) {
               $activeElement.removeClass(value);
               $(this).closest('.buttons').remove();
            }
         });

         $('#addMargin').on('click', function () {
            let device = $device.find('.button.active').data('type');
            let type = $type.find('.button.active').data('class');
            let size = $size.find('.button.active').data('class');

            if (device !== undefined && size !== undefined) {
               switch (device) {
                  case 'screen':
                     device = '';
                     break;

                  default:
                     device = '-' + device;
                     break;
               }
               switch (size) {
                  case 'default':
                     size = '';
                     break;

                  default:
                     size = '-' + size;
                     break;
               }

               switch (type) {
                  case 'default':
                     type = '';
                     break;

                  default:
                     type = '-' + type;
                     break;
               }

               finalClass = 'margin' + size + type + device;

               margin = ''
                 + '<div class="wojo wojo mini buttons" data-value="' + finalClass + '">'
                 + '<a class="wojo mini secondary inverted button passive start">'
                 + '<i class="icon ' + iconDevice + '"></i>' + sizeText + ' </a>'
                 + '<a class="wojo icon mini secondary inverted passive button auto">'
                 + '<i class="' + iconType + '"></i>'
                 + '</a>'
                 + '<a data-class="' + finalClass + '" class="wojo icon mini primary inverted button auto removeMargin">'
                 + '<i class="icon x alt"></i>'
                 + '</a>'
                 + '</div>';

               let exists = $margins.children('.buttons[data-value=\'' + finalClass + '\']').length;

               if (!exists && $activeElement !== null && $activeElement !== undefined) {
                  $margins.append(margin);
                  $activeElement.addClass(finalClass);
               }
            }
         });
      },

      /**
       * Description
       * @method _textSection
       * @return
       */
      _textSection: function () {
         $('#text_decoration').on('click', '.button', function () {
            let value = $(this).data('class');
            if ($activeElement !== null && $activeElement !== undefined) {
               $activeElement.toggleClass(value);
               $(this).toggleClass('active');
            }
         });

         $('#text_transform').on('click', '.button', function () {
            let value = $(this).data('class');
            if ($activeElement !== null && $activeElement !== undefined) {
               $('#text_transform .button').removeClass('active');
               $(this).addClass('active');
               $activeElement.removeClass('lowercase-text uppercase-text capitalize-text');
               if (value !== 'none') {
                  $activeElement.addClass(value);
               }
            }
         });

         $('#text_size').on('click', '.button', function () {
            let value = $(this).data('class');
            if ($activeElement !== null && $activeElement !== undefined) {
               $('#text_size .button').removeClass('active');
               $(this).addClass('active');
               $activeElement.alterClass('text-size-*');
               if (value !== 'text-size-normal') {
                  $activeElement.addClass(value);
               }
            }
         });

         $('#text_weight').on('click', '.button', function () {
            let value = $(this).data('class');
            if ($activeElement !== null && $activeElement !== undefined) {
               $('#text_weight .button').removeClass('active');
               $(this).addClass('active');
               $activeElement.alterClass('text-weight-*');
               if (value !== 'none') {
                  $activeElement.addClass(value);
               }
            }
         });

         $('#text_color').on('click', '.button', function () {
            let value = $(this).data('text');
            if ($activeElement !== null && $activeElement !== undefined) {
               $('#text_color .button').removeClass('active');
               $(this).addClass('active');
               $activeElement.alterClass('text-color-*');
               if (value !== 'none') {
                  $activeElement.addClass(value);
               }
            }
         });

         $('#text_align').on('click', '.button', function () {
            let value = $(this).data('class');
            if ($activeElement !== null && $activeElement !== undefined) {
               let active = value.split('-');
               switch (active[0]) {
                  case 'screen':
                     $('#text_align .button[data-class=\'screen-left-align\']').removeClass('active');
                     $('#text_align .button[data-class=\'screen-center-align\']').removeClass('active');
                     $('#text_align .button[data-class=\'screen-right-align\']').removeClass('active');
                     $('#text_align .button[data-class=\'screen-' + active[1] + '-align\']').addClass('active');
                     $activeElement.removeClass('screen-left-align screen-center-align screen-right-align');
                     if (active[1] !== 'none') {
                        $activeElement.addClass('screen-' + active[1] + '-align');
                     }
                     break;
                  case 'tablet':
                     $('#text_align .button[data-class=\'tablet-left-align\']').removeClass('active');
                     $('#text_align .button[data-class=\'tablet-center-align\']').removeClass('active');
                     $('#text_align .button[data-class=\'tablet-right-align\']').removeClass('active');
                     $('#text_align .button[data-class=\'tablet-' + active[1] + '-align\']').addClass('active');
                     $activeElement.removeClass('tablet-left-align tablet-center-align tablet-right-align');
                     if (active[1] !== 'none') {
                        $activeElement.addClass('tablet-' + active[1] + '-align');
                     }
                     break;
                  case 'mobile':
                     $('#text_align .button[data-class=\'mobile-left-align\']').removeClass('active');
                     $('#text_align .button[data-class=\'mobile-center-align\']').removeClass('active');
                     $('#text_align .button[data-class=\'mobile-right-align\']').removeClass('active');
                     $('#text_align .button[data-class=\'mobile-' + active[1] + '-align\']').addClass('active');
                     $activeElement.removeClass('mobile-left-align mobile-center-align mobile-right-align');
                     if (active[1] !== 'none') {
                        $activeElement.addClass('mobile-' + active[1] + '-align');
                     }
                     break;
                  case 'phone':
                     $('#text_align .button[data-class=\'phone-left-align\']').removeClass('active');
                     $('#text_align .button[data-class=\'phone-center-align\']').removeClass('active');
                     $('#text_align .button[data-class=\'phone-right-align\']').removeClass('active');
                     $('#text_align .button[data-class=\'phone-' + active[1] + '-align\']').addClass('active');
                     $activeElement.removeClass('phone-left-align phone-center-align phone-right-align');
                     if (active[1] !== 'none') {
                        $activeElement.addClass('phone-' + active[1] + '-align');
                     }
                     break;
                  default:
                     $('#text_align .button[data-class=\'left-align\']').removeClass('active');
                     $('#text_align .button[data-class=\'center-align\']').removeClass('active');
                     $('#text_align .button[data-class=\'right-align\']').removeClass('active');
                     $('#text_align .button[data-class=\'' + active[0] + '-align\']').addClass('active');
                     $activeElement.removeClass('left-align center-align right-align');
                     if (active[0] !== 'none') {
                        $activeElement.addClass(active[0] + '-align');
                     }
                     break;
               }
            }
         });
      },

      /**
       * Description
       * @method _rowSection
       * @return
       */
      _rowSection: function () {
         $('#rows_align').on('click', '.button', function () {
            let value = $(this).data('class');
            if ($activeElement !== null && $activeElement !== undefined) {
               $('#rows_align .button').removeClass('active');
               $(this).addClass('active');
               $activeElement.removeClass('align-top align-middle align-bottom');
               $activeElement.addClass(value);
            }
         });

         $('#rows_justify').on('click', '.button', function () {
            let value = $(this).data('class');
            if ($activeElement !== null && $activeElement !== undefined) {
               $('#rows_justify .button').removeClass('active');
               $(this).addClass('active');
               $activeElement.removeClass(function (index, className) {
                  return (className.match(/(^|\s)justify-\S+/g) || []).join(' ');
               });
               $activeElement.addClass(value);
            }
         });

         $('#rows_gutters').on('click', '.button', function () {
            let value = $(this).data('class');
            if ($activeElement !== null && $activeElement !== undefined) {
               $('#rows_gutters .button').removeClass('active');
               $(this).addClass('active');
               $activeElement.removeClass('gutters mini-gutters small-gutters medium-gutters large-gutters big-gutters huge-gutters');
               if (value !== 'none') {
                  $activeElement.addClass(value);
               }
            }
         });
      },

      /**
       * Description
       * @method _columnsSection
       * @private
       */
      _columnsSection: function () {
         let $size = $('#columns_size');
         let $order = $('#columns_order');
         let size = 's-10 s-15 s-20 s-25 s-30 s-33 s-40 s-50 s-60 s-70 s-75 s-80 s-90 s-100 auto';
         let order = 'o-1 o-2 o-3 o-4 o-5 o-6';

         $size.on('change', 'select', function () {
            let value = $(this).val();
            let name = $(this).attr('name');

            if ($activeElement !== null && $activeElement !== undefined) {
               $activeElement.removeClass(size.replace(/s/g, name));
               if (value !== 'none') {
                  $activeElement.addClass(name + '-' + value);
               }
            }
         });

         $order.on('change', 'select', function () {
            let value = $(this).val();
            let name = $(this).attr('name');

            if ($activeElement !== null && $activeElement !== undefined) {
               $activeElement.removeClass(order.replace(/o/g, name + '-order'));
               if (value !== 'none') {
                  $activeElement.addClass(name + '-order-' + value);
               }
            }
         });

         $size.on('click', '.button', function () {
            if ($activeElement !== null && $activeElement !== undefined) {
               $(this).toggleClass('active');
               if ($(this).is('.active')) {
                  $activeElement.removeClass(size.replace(/s/g, 'screen'))
                    .removeClass(size.replace(/s/g, 'tablet'))
                    .removeClass(size.replace(/s/g, 'mobile'))
                    .removeClass(size.replace(/s/g, 'phone'));
                  $activeElement.addClass('auto');
                  $('section[data-helper=\'columns\']  select').val('none');
               } else {
                  $activeElement.removeClass('auto');
               }
            }
         });
      },

      /**
       * Description
       * @method _displaySection
       * @private
       */
      _displaySection: function () {
         $('#display_type').on('click', '.button', function () {
            let value = $(this).data('class');
            if ($activeElement !== null && $activeElement !== undefined) {
               $('#display_type .button').removeClass('active');
               $(this).addClass('active');
               $activeElement.removeClass('display-inline display-inline-block display-block display-flex display-inline-flex');
               if (value !== 'none') {
                  $activeElement.addClass(value);
               }
            }
         });
      },

      /**
       * Description
       * @method _positionSection
       * @private
       */
      _positionSection: function () {
         $('#position_type').on('click', '.button', function () {
            let value = $(this).data('class');
            const $pp = $('#p_place');
            const $pi = $('#p_index');
            if ($activeElement !== null && $activeElement !== undefined) {
               $('#position_type .button').removeClass('active');
               $(this).addClass('active');
               $activeElement.removeClass('static relative absolute fixed');
               $activeElement.addClass(value);

               if (value === 'static') {
                  $pp.addClass('hide-all');
                  $pi.addClass('hide-all');
               } else {
                  $pp.removeClass('hide-all');
                  $pi.removeClass('hide-all');
               }
            }
         });

         $('#position_index').on('click', '.button', function () {
            let value = $(this).data('class');
            if ($activeElement !== null && $activeElement !== undefined) {
               $('#position_index .button').removeClass('active');
               $(this).addClass('active');
               $activeElement.removeClass(function (index, className) {
                  return (className.match(/(^|\s)zindex\S+/g) || []).join(' ');
               });
               if (value !== 'none') {
                  $activeElement.addClass(value);
               }
            }
         });

         $('#position_place').on('click', '.button', function () {
            let value = $(this).data('class');
            let pos = value.split('-');
            let final;
            let remove;

            switch (pos[1]) {
               case '100':
                  final = 'position-' + pos[0] + '-100';
                  remove = 'position-' + pos[0] + '-50 position-' + pos[0];
                  break;

               case '50':
                  final = 'position-' + pos[0] + '-50';
                  remove = 'position-' + pos[0] + '-100 position-' + pos[0];
                  break;

               case 'zero':
                  final = 'position-' + pos[0];
                  remove = 'position-' + pos[0] + '-50 position-100';
                  break;

               default:
                  final = 'none';
                  remove = 'position-' + pos[0] + '-50 position-' + pos[0] + ' position-' + pos[0] + '-100';
                  break;

            }

            if ($activeElement !== null && $activeElement !== undefined) {
               $('#position_place_' + pos[0] + ' .button').not('.button[data-type=icon]').removeClass('active');
               $(this).toggleClass('active');

               $activeElement.removeClass(remove);
               if (final !== 'none') {
                  $activeElement.addClass(final);
               }
            }
         });
      },

      /**
       * Description
       * @method _borderSection
       * @private
       */
      _borderSection: function () {
         let borderType;
         let removeType;
         let borderSize;
         let removeSize = 'border-1 border-2 border-3 border-4 border-5';
         let borderRadius;
         let removeRadius;

         $('#border_type').on('click', '.button', function () {
            let value = $(this).data('class');
            let $btb = $('#border_type .button');
            let $bsb = $('#border_size .button');
            let $bcb = $('#border_color .button');
            switch (value) {
               case'border-full':
                  $btb.removeClass('active');
                  $(this).addClass('active');
                  borderType = 'border';
                  removeType = 'border-top border-bottom border-left border-right';
                  break;

               case'none':
                  $(this).addClass('active');
                  borderType = 'none';
                  removeType = removeSize + ' border border-top border-bottom border-left border-right';
                  break;

               default:
                  $('#border_type .button[data-class=border-full]').removeClass('active');
                  $('#border_type .button[data-class=none]').removeClass('active');
                  $(this).toggleClass('active');
                  if ($(this).is('.active')) {
                     borderType = value;
                     removeType = 'border';
                  } else {
                     borderType = 'none';
                     removeType = value;
                  }
                  break;
            }
            if ($activeElement !== null && $activeElement !== undefined) {
               $activeElement.removeClass(removeType);
               if (borderType === 'none') {
                  $btb.removeClass('active');
                  $bsb.removeClass('active');
                  $bcb.removeClass('active');
                  $activeElement.alterClass('border-color-*');
               } else {
                  $activeElement.addClass(borderType);
               }
            }
         });

         $('#border_size').on('click', '.button', function () {
            let value = $(this).data('class');
            $('#border_size .button').removeClass('active');
            $(this).addClass('active');
            borderSize = value;
            if ($activeElement !== null && $activeElement !== undefined) {
               $activeElement.removeClass(removeSize);
               if (borderSize !== 'none') {
                  $activeElement.addClass(borderSize);
               }
            }
         });

         $('#border_radius').on('click', '.button', function () {
            let value = $(this).data('class');
            let $brb = $('#border_radius .button');

            switch (value) {
               case'rounded-full':
                  $brb.removeClass('active');
                  $(this).addClass('active');
                  borderRadius = 'rounded';
                  removeRadius = 'rounded-top rounded-bottom rounded-left rounded-right';
                  break;

               case'none':
                  $brb.removeClass('active');
                  $(this).addClass('active');
                  borderRadius = 'none';
                  removeRadius = 'rounded rounded-top rounded-bottom rounded-left rounded-right';
                  break;

               default:
                  $('#border_radius .button[data-class=rounded-full]').removeClass('active');
                  $('#border_radius .button[data-class=none]').removeClass('active');
                  $(this).toggleClass('active');
                  if ($(this).is('.active')) {
                     borderRadius = value;
                     removeRadius = 'rounded';
                  } else {
                     borderRadius = 'none';
                     removeRadius = value;
                  }
                  break;
            }
            if ($activeElement !== null && $activeElement !== undefined) {
               $activeElement.removeClass(removeRadius);
               if (borderRadius !== 'none') {
                  $activeElement.addClass(borderRadius);
               }
            }
         });

         $('#border_color').on('click', '.button', function () {
            let value = $(this).data('border');
            if ($activeElement !== null && $activeElement !== undefined) {
               $('#border_color .button').removeClass('active');
               $(this).addClass('active');

               $activeElement.alterClass('border-color-*');
               if (value !== 'none') {
                  $activeElement.addClass(value);
               }
            }
         });
      },

      /**
       * Description
       * @method _visibilitySection
       * @private
       */
      _visibilitySection: function () {
         $('#visibility_opacity').on('change', function () {
            let value = $(this).val();
            if ($activeElement !== null && $activeElement !== undefined) {
               $activeElement.removeClass(function (index, className) {
                  return (className.match(/(^|\s)opacity-\S+/g) || []).join(' ');
               });
               if (value !== 'none') {
                  $activeElement.addClass(value);
               }
            }
         });

         $('#visibility_overflow').on('click', '.button', function () {
            let value = $(this).data('class');
            if ($activeElement !== null && $activeElement !== undefined) {
               $('#visibility_overflow .button').removeClass('active');
               $(this).addClass('active');
               $activeElement.removeClass('overflow-hidden overflow-auto');
               if (value !== 'none') {
                  $activeElement.addClass(value);
               }
            }
         });

         $('#visibility_visibility').on('click', '.button', function () {
            let value = $(this).data('class');
            if ($activeElement !== null && $activeElement !== undefined) {
               let active = value.split('-');
               let $vs = $('#visibility_visibility .button[data-class=\'' + active[0] + '-show\']');
               let $vh = $('#visibility_visibility .button[data-class=\'' + active[0] + '-hide\']');
               let $vn = $('#visibility_visibility .button[data-class=\'' + active[0] + '-none\']');
               switch (active[1]) {
                  case 'hide':
                     $vs.removeClass('active');
                     $vn.removeClass('active');
                     $activeElement.removeClass(active[0] + '-show').addClass(active[0] + '-hide');
                     break;
                  case 'show':
                     $vh.removeClass('active');
                     $vn.removeClass('active');
                     $activeElement.removeClass(active[0] + '-hide').addClass(active[0] + '-show');
                     break;
                  default:
                     $vh.removeClass('active');
                     $vs.removeClass('active');
                     $activeElement.removeClass(active[0] + '-hide ' + active[0] + '-show');
                     break;
               }
               $(this).addClass('active');
            }
         });
      },

      /**
       * Description
       * @method _backgroundSection
       * @private
       */
      _backgroundSection: function () {
         let plugin = this;

         const $color = $('#background_color');
         const $position = $('#background_position');
         const $size = $('#background_size');
         const $image = $('#background_image');
         const $holder = $('#bgImageHolder');
         const $fixed = $('#background_fixed');
         const $repeat = $('#background_repeat');
         const $clear = $('#background_clear');

         $color.on('click', '.button', function () {
            let value = $(this).data('class');
            if ($activeElement !== null && $activeElement !== undefined) {
               $('#background_color .button').removeClass('active');
               $(this).addClass('active');
               $activeElement.alterClass('bg-color-*');
               if (value !== 'none') {
                  $activeElement.addClass('bg-color-' + value);
               }
            }
         });

         $image.on('click', function () {
            $.ajax({
               url: plugin.options.url + '/filepicker.php',
               type: 'GET',
               data: {
                  pickFile: 1,
                  editor: true
               },
               async: true
            }).done(function (data) {
               $('<div class="wojo big modal"><div class="dialog" role="document"><div class="content">' + data + '</div></div></div>').modal();
               $('#result').on('click', '.is_file', function () {
                  let dataset = $(this).data('set');
                  if (dataset.image === 'true') {
                     $holder.html('<img src="' + plugin.options.upurl + '/' + dataset.url + '" alt="">');
                     if ($activeElement !== null && $activeElement !== undefined) {
                        $activeElement.attr('data-image', dataset.url);
                        $activeElement.css({
                           'backgroundImage': 'url(' + plugin.options.upurl + '/' + dataset.url + ')',
                        });
                     }
                     $.modal.close();
                  }
               });
            });
         });

         $clear.on('click', function () {
            if ($activeElement !== null && $activeElement !== undefined) {
               $('#background_position .button').removeClass('active');
               $('#background_size .button').removeClass('active');
               $fixed.prop('checked', false);
               $repeat.prop('checked', false);
               $holder.html('');

               $activeElement.alterClass('bg-position-* bg-size-* bg-fixed* bg-repeat-*');
               $activeElement.removeAttr('style data-image');
            }
         });

         $position.on('click', '.button', function () {
            let value = $(this).data('class');
            if ($activeElement !== null && $activeElement !== undefined) {
               $('#background_position .button').removeClass('active');
               $(this).addClass('active');
               $activeElement.alterClass('bg-position-*', value);
            }
         });

         $size.on('click', '.button', function () {
            let value = $(this).data('class');
            if ($activeElement !== null && $activeElement !== undefined) {
               $('#background_size .button').removeClass('active');
               $(this).addClass('active');
               $activeElement.alterClass('bg-size-*', value);
            }
         });

         $fixed.on('change', function () {
            if ($activeElement !== null && $activeElement !== undefined) {
               if ($(this).prop('checked')) {
                  $activeElement.addClass('bg-fixed');
               } else {
                  $activeElement.removeClass('bg-fixed');
               }
            }
         });

         $repeat.on('change', function () {
            if ($activeElement !== null && $activeElement !== undefined) {
               if ($(this).prop('checked')) {
                  $activeElement.addClass('bg-repeat-none');
               } else {
                  $activeElement.removeClass('bg-repeat-none');
               }
            }
         });
      },

      /**
       * Description
       * @method _advancedSection
       * @return
       */
      _advancedSection: function () {
         const $advid = $('#advanced_id');
         const $attname = $('#attributes_name');

         $attname.on('click', '.button', function () {
            $attname.find('.button').removeClass('active');
            $(this).addClass('active');
         });

         $('#advanced_classes').on('click', '.label a', function () {
            let value = $(this).parent('.label').data('value');
            if ($activeElement !== null && $activeElement !== undefined) {
               $(this).parent('.label').remove();
               $activeElement.removeClass(value);
            }
         });

         $advid.on('click', '.label a', function () {
            if ($activeElement !== null && $activeElement !== undefined) {
               $(this).parent('.label').remove();
               $activeElement.removeAttr('id');
            }
         });

         $('#addAttribute').on('click', function () {
            let value = $('input[name=attributes_value]').val();
            let name = $('#attributes_name .button.active').data('value');
            if ($.trim(name).length > 0 && $.trim(value).length > 0) {
               value = value.replace(/[^a-zA-Z0-9-]/g, '');
               if ($activeElement !== null && $activeElement !== undefined) {
                  if (name === 'id') {
                     if ($advid.children().length) {
                        $('#advanced_id .label').remove();
                     }
                     $advid.prepend('<div class="wojo mini right dark inverted label" data-value="' + value + '">'
                       + '' + value + '<a class="inline-flex"><i class="icon x negative alt"></i></a></div>');
                     $activeElement.attr('id', value);
                  } else {
                     if ($('#advanced_classes .label[data-value=\'' + value + '\']').length < 1) {
                        $('#advanced_classes').prepend('<div class="wojo mini right dark inverted label" data-value="' + value + '">'
                          + '' + value + '<a class="inline-flex"><i class="icon x negative alt"></i></a></div> ');
                        $activeElement.addClass(value);
                     }
                  }
               }
            }
         });
      },

      /**
       * Description
       * @method _linkList
       * @param element
       * @param active
       * @private
       */
      _linkList: function (element, active) {
         let list = '';
         let plugin = this;
         if (element.has('option').length < 1) {
            $.get(plugin.options.aurl + 'ajax/', {
                 action: 'getlinks',
                 is_builder: 1
              },
              function (json) {
                 list += '<option value="!#">--- Custom ---</option>';
                 $.each(json.message, function (i, item) {
                    let selected = (item.href === active) ? 'selected="selected"' : null;
                    list += '<option value="' + item.href + '" ' + selected + '>' + item.name + '</option>';
                 });
                 element.append(list);
              }, 'json');
         }
      },

      /**
       * Description
       * @method _loadModules
       * @param alias
       * @return
       */
      _loadModules: function (alias) {
         let plugin = this;
         $.get(plugin.options.aurl + 'builder/action/', {
            action: 'loadModules',
            modalias: alias
         }).done(function (json) {
            let jsonObj = JSON.parse(json);
            if (jsonObj.status === 'success') {
               $('#tab_modules').find('.row').append(jsonObj.html);
            }

         }, 'json');
      },

      /**
       * Description
       * @method _parseButton
       * @private
       */
      _parseButton: function () {
         let plugin = this;
         const $bsize = $('#button_size');
         const $bstyle = $('#button_style');
         const $bwidth = $('#button_width');
         const $bpos = $('#button_position');
         const $bcolor = $('#button_color');
         const $icons = $('#b_icons');
         //const $url = $("#basic_url_text");
         //const $link = $("#basic_url");

         let size, style, width, position, color, icon, single;
         //let text = plugin.cleanText($url.val());

         size = $bsize.find('.button.active').data('class');
         style = $bstyle.find('.button.active').data('class');
         width = $bwidth.find('.button.active').data('class');
         position = $bpos.find('.button.active').data('class');
         color = $bcolor.find('.button.active').data('class');
         icon = $icons.find('.button.active');

         size = (size === 'default') ? size.replace('default', '') : ' ' + size;
         style = (style === 'default') ? style.replace('default', '') : ' ' + style;
         width = (width === 'auto') ? width.replace('auto', '') : ' ' + width;
         position = (position === 'default') ? position.replace('default', '') : ' ' + position;

         let bcolor = color.split('-');
         if (bcolor.length === 2) {
            color = ' ' + bcolor[0] + ' inverted';
         } else {
            color = ' ' + color;
         }

         if (icon.length) {
            //let $id = $activeElement.children(".icon").attr("nav-id");
            //let labels = 'wf-type="icon" wf-label="Icon" nav-id="' + $id;
            //icon_html = '<i class="icon ' + icon.data("class") + '" ' + labels + '"></i>';
            //} else {
            //icon_html = "";
         }

         //icon_space = ($.trim(position) === "right") ? text + icon_html : icon_html + text;

         if ($.trim(style) === 'icon' || $.trim(style) === 'circular') {
            //icon_space = icon_html;
            single = ' icon ';
         } else {
            single = '';
         }

         $activeElement.keepClasses('wojo button player middle top bottom left right attached lightbox live');
         $activeElement.addClass(single + color + style + width + size + position);
         if ($.trim(position) === 'right') {
            $activeElement.find('span').prependTo($activeElement);
         } else {
            $activeElement.find('.icon').prependTo($activeElement);
         }
         /*
                  let $id = $activeElement.attr("nav-id");
                  let button = $("<a href=\"" + plugin.cleanText($link.val()) + "\">").attr({
                     "nav-id": $id,
                     "wf-type": "link",
                     "wf-label": "Link",
                     "class": "wojo button" + single + color + style + width + size + position + " live",
                  }).html(icon_space);

                  $activeElement.replaceWith(button);
                  $activeElement = button;*/
      },

      /**
       * Description
       * @method _offEvents
       * @return
       */
      _offEvents: function () {
         $(this.element).off('mouseenter mouseleave mouseover mouseout');
      },

      /**
       * Description
       * @method _onEvents
       * @return
       */
      _onEvents: function () {
         /*
            $(this.element).on({
                mouseover: function(event) {
                    if (typeof $(event.target).attr("data-weditable") !== "undefined") {
                        $(event.target).addClass("active");
                        $(event.target).closest(".section").removeClass("active");
                    } else {
                        $(this).addClass("active");
                    }

                },
                mouseout: function() {
                    $(this).removeClass("active");
                }
            }, '.section, [data-weditable]');*/
      },

      /**
       * Description
       * @method _editHtml
       * @return
       */
      _editHtml: function () {
         let $source = $('#editSource');
         $('#advanced_html').on('click', '.button', function () {
            if ($activeElement !== null && $activeElement !== undefined) {
               if ($activeElement.is('.section')) {
                  $activeElement.find('.grid-insert').remove();
               }
               let html = $activeElement.html();
               htmlEditor.setValue(html);
               $('#mainFrame').addClass('overlay');
               $('#builderViewer').contents().find('#builderFrame').addClass('overlay')
               $('#editSource').transition('slideInUp').removeClass('hidden');
            }
         });

         $source.on('click', '.action .ok', function () {
            if ($activeElement !== null && $activeElement !== undefined) {
               $activeElement.html(htmlEditor.getValue());
               if ($activeElement.is('.section') && !$activeElement.find('.grid-insert').length) {
                  $activeElement.append(wraps.gridInsert);
                  $activeElement.append(wraps.gridMove);
               }
            }
         });

         $source.on('click', '.action .cancel', function () {
            $source.transition('slideOutDown', {
               duration: 200,
               complete: function () {
                  $('#mainFrame').removeClass('overlay');
                  $('#builderViewer').contents().find('#builderFrame').removeClass('overlay')
                  $source.addClass('hidden');
               }
            });

         });

      },

      /**
       * Description
       * @method _deleteBlock
       * @param type
       * @private
       */
      _deleteBlock: function (type) {
         let plugin = this;
         let dataId = $('#builderNav').attr('data-id');
         let plugins = [];
         let modules = [];

         switch (type) {
            case 'section':
               $('[data-wplugin-id]', $activeSection).each(function () {
                  plugins.push($(this).attr('data-wplugin-id'));
               });

               $('[data-wmodule-id]', $activeSection).each(function () {
                  modules.push($(this).attr('data-wmodule-alias'));
               });

               $activeSection.transition('scaleOut', {
                  duration: 200,
                  complete: function () {
                     $(this).remove();
                     $('#builderNav').removeClass('open').html('');
                  }
               });

               break;
            case 'element':
               let $parent = $activeElement.parent();
               $('[data-wplugin-id]', $parent).each(function () {
                  plugins.push($(this).attr('data-wplugin-id'));
               });

               $('[data-wmodule-id]', $parent).each(function () {
                  modules.push($(this).attr('data-wmodule-alias'));
               });

               $activeElement.transition('scaleOut', {
                  duration: 200,
                  complete: function () {
                     $(this).remove();
                     let $el = $(plugin.element).find('.section[data-id=' + dataId + ']');
                     $('#builderNav').html($el.makeNav());
                  }
               });
               $activeElement = null;
               if ($parent.is('.columns')) {
                  $parent.addClass('is_empty');
               }
               break;
         }

         if (plugins.length > 0) {
         }
         if (modules.length > 0) {
            plugin._loadModules(modules);
         }
      },
      /**
       *
       * @private
       */
      _moveBlock: function () {
         $(this.element).on('click', '.grid-move a', function (node, child) {
            let element = $(this).closest('.section');
            console.log(element);
            if ($(this).is('a.up')) {
               let previous = element.prev('.section');
               if (previous.length !== 0) {
                  $(element).insertBefore(previous, child);
               }
            } else {
               let next = element.next('.section');
               if (next.length !== 0) {
                  element.insertAfter(next);
               }
            }
            return false;
         });
      },

      /**
       * Description
       * @method cleanText
       * @param text
       * @returns {*}
       */
      cleanText: function (text) {
         let regex = /(<([^>]+)>)/ig;

         return (text !== undefined) ? text.replace(regex, '') : '';
      },

      /**
       * Description
       * @method prepareRow
       * @return
       * @param section
       */
      prepareRow: function (section) {
         let plugin = this;

         let id = plugin.makeid();
         $(section).attr({
            'data-id': id,
            'wf-type': 'section',
            'wf-label': 'Section'
         });
         let $grid = $(section).find('.wojo-grid');
         if ($grid) {
            $grid.attr({
               'wf-type': 'wcontainer',
               'wf-label': 'Wojo Container'
            });
         }
         let $rows = $grid.find('.row');
         if ($rows) {
            $rows.attr({
               'wf-type': 'row',
               'wf-label': 'Row'
            });
         }
         let $columns = $rows.find('.columns');
         if ($columns) {
            $columns.attr({
               'wf-type': 'columns',
               'wf-label': 'Columns'
            });
         }
      },

      /**
       * Description
       * @method prepareSection
       * @return
       */
      prepareSection: function () {
         let plugin = this;
         $(this.element).children('.section').each(function () {
            let id = plugin.makeid();
            $(this).attr({
               'data-id': id,
               'wf-type': 'section',
               'wf-label': 'Section'
            });

            let $grid = $(this).find('.wojo-grid');
            if ($grid) {
               $grid.attr({
                  'wf-type': 'wcontainer',
                  'wf-label': 'Wojo Container'
               });
            }

            let $rows = $(this).find('.row');
            if ($rows) {
               $rows.attr({
                  'wf-type': 'row',
                  'wf-label': 'Row'
               });
            }
            let $columns = $rows.find('.columns');
            if ($columns) {
               $columns.attr({
                  'wf-type': 'columns',
                  'wf-label': 'Column'
               });
            }

            $columns.find('h1, h2, h3, h4, h5, h6, .heading, p, .text, .description').each(function () {
               let name = $(this).is('p, .text, .description') ? 'Paragraph' : 'Heading';
               let type;
               if ($(this).is('h1, h2, h3, h4, h5, h6') && $(this).children().length) {
                  type = 'container';
               } else {
                  type = 'text';
               }
               $(this).attr({
                  'wf-type': type,
                  'wf-label': name
               });
            });

            $columns.find('.card, .cards, .segment, .message, .list, .content, .header, .footer, blockquote').each(function () {
               $(this).addClass('container');
            });

            $columns.find('.container').each(function () {
               let name = $(this).hasClass('list') ? 'List' : 'Container';
               $(this).attr({
                  'wf-type': 'container',
                  'wf-label': name
               });
            });

            let $img = $columns.find('img');
            if ($img) {
               $img.attr({
                  'wf-type': 'img',
                  'wf-label': 'Image'
               });
            }
            let $figure = $columns.find('figure');
            if ($figure) {
               $figure.attr({
                  'wf-type': 'container',
                  'wf-label': 'Figure'
               });
            }
            let $video = $columns.find('.video');
            if ($video) {
               $video.attr({
                  'wf-type': 'video',
                  'wf-label': 'Video'
               });
            }
            let $audio = $columns.find('.soundcloud');
            if ($audio) {
               $audio.attr({
                  'wf-type': 'audio',
                  'wf-label': 'Audio'
               });
            }
            let $map = $columns.find('.google-map');
            if ($map) {
               $map.attr({
                  'wf-type': 'map',
                  'wf-label': 'Google Map'
               });
            }
            let $href = $columns.find('a');
            if ($href) {
               $href.attr({
                  'wf-type': 'link',
                  'wf-label': 'Link'
               });
            }

            $columns.find('span, .list > .item, .list > li').each(function () {
               $(this).attr({
                  'wf-type': 'text',
                  'wf-label': 'Text'
               });
            });

            $columns.find('.icon').each(function () {
               if ($(this).prop('tagName').toLowerCase() === 'i') {
                  $(this).attr({
                     'wf-type': 'icon',
                     'wf-label': 'Icon'
                  });
               }
            });

            let $label = $columns.find('.label');
            if ($label) {
               $label.attr({
                  'wf-type': 'label',
                  'wf-label': 'Label'
               });
            }
         });

         $(this.element).children('.section').append(wraps.gridInsert);
         $(this.element).children('.section').append(wraps.gridMove);

         $(this.element).find('[wf-type]').each(function (index) {
            $(this).attr('nav-id', 'nav_' + index);
         });

         $(this.element).find('[data-image]').each(function () {
            let img = $(this).attr('data-image');
            $(this).attr('style', 'background-image: url(' + plugin.options.upurl + img + ')');
         });
      },

      /**
       * Description
       * @method prepareColumn
       * @return
       * @param column
       */
      prepareColumn: function (column) {
         let plugin = this;
         const $bn = $('#builderNav');

         column.find('h1, h2, h3, h4, h5, h6, .heading, p, .text, .description').each(function () {
            let name = $(this).is('p, .text, .description') ? 'Paragraph' : 'Heading';
            let type;
            if ($(this).is('h1, h2, h3, h4, h5, h6') && $(this).children().length) {
               type = 'container';
            } else {
               type = 'text';
            }
            $(this).attr({
               'wf-type': type,
               'wf-label': name
            });
         });

         let $img = column.find('img');
         if ($img) {
            $img.attr({
               'wf-type': 'img',
               'wf-label': 'Image'
            });
         }

         let $figure = column.find('figure');
         if ($figure) {
            $figure.attr({
               'wf-type': 'container',
               'wf-label': 'Figure'
            });
         }

         let $video = column.find('.video');
         if ($video) {
            $video.attr({
               'wf-type': 'video',
               'wf-label': 'Video'
            });
         }

         let $audio = column.find('.soundcloud');
         if ($audio) {
            $audio.attr({
               'wf-type': 'audio',
               'wf-label': 'Audio'
            });
         }

         let $map = column.find('.google-map');
         if ($map) {
            $map.attr({
               'wf-type': 'map',
               'wf-label': 'Google Map'
            });
         }

         let $href = column.find('a');
         if ($href) {
            $href.attr({
               'wf-type': 'link',
               'wf-label': 'Link'
            });
         }
         column.find('span, .list > .item, .list > li').each(function () {
            $(this).attr({
               'wf-type': 'text',
               'wf-label': 'Text'
            });
         });

         column.find('.icon').each(function () {
            if ($(this).prop('tagName').toLowerCase() === 'i') {
               $(this).attr({
                  'wf-type': 'icon',
                  'wf-label': 'Icon'
               });
            }
         });

         let $label = column.find('.label');
         if ($label) {
            $label.attr({
               'wf-type': 'label',
               'wf-label': 'Label'
            });
         }

         $(plugin.element).find('[wf-type]').each(function (index) {
            $(this).attr('nav-id', 'nav_' + index);
         });
         column.makeNav();

         let dataId = $bn.attr('data-id');
         let $el = $(plugin.element).find('.section[data-id=' + dataId + ']');
         $bn.html($el.makeNav());
      },

      /**
       * Description
       * @method makeRows
       * @param row
       * @return
       */
      makeRows: function (row) {
         let plugin = this;
         let html = '';
         switch (row) {

            case 2:
               html += '' +
                 '<div class="columns mobile-50 phone-100 is_empty"></div>' +
                 '<div class="columns mobile-50 phone-100 is_empty"></div>';
               break;

            case 3:
               html += '' +
                 '<div class="columns phone-100 is_empty"></div>' +
                 '<div class="columns phone-100 is_empty"></div>' +
                 '<div class="columns phone-100 is_empty"></div>';
               break;

            case 4:
               html += '' +
                 '<div class="columns tablet-50 mobile-100 phone-100 is_empty"></div>' +
                 '<div class="columns tablet-50 mobile-100 phone-100 is_empty"></div>' +
                 '<div class="columns tablet-50 mobile-100 phone-100 is_empty"></div>' +
                 '<div class="columns tablet-50 mobile-100 phone-100 is_empty"></div>';
               break;

            case 5:
               html += '' +
                 '<div class="columns screen-60 tablet-60 mobile-50 phone-100 is_empty"></div>' +
                 '<div class="columns screen-40 tablet-40 mobile-50 phone-100 is_empty"></div>';
               break;

            case 6:
               html += '' +
                 '<div class="columns screen-40 tablet-40 mobile-50 phone-100 is_empty"></div>' +
                 '<div class="columns screen-60 tablet-60 mobile-50 phone-100 is_empty"></div>';
               break;

            case 7:
               html += '' +
                 '<div class="columns screen-30 tablet-30 mobile-50 phone-100 is_empty"></div>' +
                 '<div class="columns screen-70 tablet-70 mobile-50 phone-100 is_empty"></div>';
               break;

            case 8:
               html += '' +
                 '<div class="columns screen-70 tablet-70 mobile-50 phone-100 is_empty"></div>' +
                 '<div class="columns screen-30 tablet-30 mobile-50 phone-100 is_empty"></div>';
               break;

            case 9:
               html += '' +
                 '<div class="columns screen-20 tablet-20 mobile-100 phone-100 is_empty"></div>' +
                 '<div class="columns screen-60 tablet-60 mobile-100 phone-100 is_empty"></div>' +
                 '<div class="columns screen-20 tablet-20 mobile-100 phone-100 is_empty"></div>';
               break;

            case 10:
               html += '' +
                 '<div class="columns screen-20 tablet-20 mobile-100 phone-100 is_empty"></div>' +
                 '<div class="columns screen-20 tablet-20 mobile-100 phone-100 is_empty"></div>' +
                 '<div class="columns screen-60 tablet-60 mobile-100 phone-100 is_empty"></div>';
               break;

            case 11:
               html += '' +
                 '<div class="columns screen-60 tablet-60 mobile-100 phone-100 is_empty"></div>' +
                 '<div class="columns screen-20 tablet-20 mobile-100 phone-100 is_empty"></div>' +
                 '<div class="columns screen-20 tablet-20 mobile-100 phone-100 is_empty"></div>';
               break;

            default:
               html += '<div class="columns is_empty"></div>';
               break;
         }

         let el = $('<div class="section"><div class="wojo-grid"><div class="row gutters">' + html + '</div></div></div>').append(wraps.gridInsert).append(wraps.gridMove);
         plugin.prepareRow(el);
         el.insertAfter($activeSection);
         $(el).makeNav();
         $(plugin.element).find('[wf-type]').each(function (index) {
            $(this).attr('nav-id', 'nav_' + index);
         });
      },

      /**
       * Description
       * @method makeid
       * @returns {string}
       */
      makeid: function () {
         let text = '';
         let possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
         for (let i = 0; i < 2; i++) {
            text += possible.charAt(Math.floor(Math.random() * possible.length));
         }
         let text2 = '';
         let possible2 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
         for (let k = 0; k < 5; k++) {
            text2 += possible2.charAt(Math.floor(Math.random() * possible2.length));
         }
         return text + text2;
      },

      /**
       * Description
       * @method destroy
       * @return
       */
      destroy: function () {
         this.unbindEvents();
         this.$element.removeData();
      },

      /**
       *
       * @returns {Selection|*|string}
       */
      getSelectedText: function () {
         const frame = document.getElementById('builderViewer');
         let frameWindow = frame.contentWindow;
         let frameDocument = frameWindow.document;

         if (frameWindow.getSelection) {
            return frameWindow.getSelection().toString();
         } else if (frameDocument.getSelection) {
            return frameDocument.getSelection();
         } else if (frameDocument.selection) {
            return frameDocument.selection.createRange().text;
         }
      }
   });

   /**
    * Description
    * @method makeNav
    * @param isTop
    * @returns {*|jQuery|HTMLElement|string}
    */
   $.fn.makeNav = function (isTop = null) {
      if (isTop == null) {
         isTop = true;
      }
      const ul = $('<ul>');
      const li = $('<li>');
      li.append('<div>' + $(this).attr('wf-label') + '<a><i class="icon x alt"></a></div>');
      li.attr({
         'nav-id': $(this).attr('nav-id'),
         'class': 'wf-' + $(this).attr('wf-type')
      });
      let children = 0;
      $(this).children('[wf-type]').each(function () {
         ul.append($(this).makeNav(false));
         children++;
      });
      if (children > 0) {
         li.append(ul);
      }
      if (li.is(':empty')) {
         return '';
      }
      if (isTop) {
         return $('<ul>').append(li);
      }
      return li;
   };

   /**
    * Description
    * Remove element classes with wildcard matching. Optionally add classes:
    * $('#foo').alterClass('foo-* bar-*', 'foobar')
    * @method alterClass
    * @returns {*|jQuery|HTMLElement|string}
    * @param removals
    * @param additions
    */
   $.fn.alterClass = function (removals, additions = '') {
      const self = this;

      if (removals.indexOf('*') === -1) {
         self.removeClass(removals);
         return !additions ? self : self.addClass(additions);
      }

      let patt = new RegExp('\\s' + removals.replace(/\*/g, '[A-Za-z0-9-_]+').split(' ').join('\\s|\\s') + '\\s', 'g');

      self.each(function (i, it) {
         let cn = ' ' + it.className + ' ';
         while (patt.test(cn)) {
            cn = cn.replace(patt, ' ');
         }
         it.className = $.trim(cn);
      });


      return !additions ? self : self.addClass(additions);
   };

   /**
    * Description
    * Remove all classes except "first second third":
    * @method keepClasses
    * @param classList
    * @returns {*}
    */
   $.fn.keepClasses = function (classList) {
      return this.each(function (index, el) {
         let keep = classList.split(' ');
         let reAdd = [];
         let $el = $(el);

         for (let c = 0; c < keep.length; c++) {
            if ($el.hasClass(keep[c])) reAdd.push(keep[c]);
         }
         $el.removeClass().addClass(reAdd.join(' '));
      });
   };

   /**
    * Description
    * draggable
    * @method drags
    * @param options
    * @returns {*}
    */
   $.fn.draggable = function (options) {
      let defaults = {
         headerIdentifier: '.drag-handle',
         position: {
            dock: null,
            top: null,
            left: null
         }
      };

      let settings = $.extend({}, defaults, options);

      this.filter('div.is_draggable').each(function () {
         let item = $(this);
         if (settings.position.dock) {
            let dockableItem = $(settings.position.dock);

            if (!dockableItem) {
               return;
            }

            let y = settings.position.top ?? 0;
            let x = settings.position.left ?? 0;

            item.css({
               top: dockableItem.offset().top + y,
               left: dockableItem.offset().left + x
            });

            return;
         }

         if (settings.position.top) {
            item.css({
               top: settings.position.top
            });
         }

         if (settings.position.left) {
            item.css({
               left: settings.position.left
            });
         }

         let headerItem = item.find(settings.headerIdentifier) ?? item;

         headerItem.unbind('mousedown');
         $(document).unbind('mouseup mousemove');

         let pos1 = 0,
           pos2 = 0,
           pos3 = 0,
           pos4 = 0;

         headerItem.on('mousedown', function (mouseDownEvent) {
            mouseDownEvent = mouseDownEvent || window.event;
            mouseDownEvent.preventDefault();
            pos3 = mouseDownEvent.clientX;
            pos4 = mouseDownEvent.clientY;
            $(document).on('mousemove', function (moveEvent) {
               moveEvent = moveEvent || window.event;
               moveEvent.preventDefault();
               // calculate the new cursor position:
               pos1 = pos3 - moveEvent.clientX;
               pos2 = pos4 - moveEvent.clientY;
               pos3 = moveEvent.clientX;
               pos4 = moveEvent.clientY;
               // set the element's new position:
               item.offset({
                  top: item.offset().top - pos2,
                  left: item.offset().left - pos1
               });
            });
            $(document).on('mouseup', function () {
               $(document).unbind('mouseup mousemove');
            });
         });

      });

      return this;
   };

   /**
    * Description
    * @method Builder
    * @param options
    * @returns {jQuery.Builder}
    * @constructor
    */
   $.fn.Builder = function (options) {
      this.each(function () {
         if (!$.data(this, pluginName)) {
            $.data(this, pluginName, new Plugin(this, options));
         }
      });
      return this;
   };

   $.fn.Builder.defaults = {
      editables: ['div', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'i', 'span'],
      aurl: '',
      url: '',
      surl: '',
      burl: '',
      upurl: '',
      pagename: '',
      lang: {
         btnOk: 'ok',
         btnCancel: 'cancel',
         msgUndone: 'Are you sure you want to restore it, this action can not be undone!',
         msgUrlError: 'Invalid url detected!!!',
      }
   };

})(jQuery, window, document);
