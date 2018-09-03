(function ($, window, document, undefined) {

    var pluginName = "dynamicTable",
        defaults = {};

    function Plugin(element, options) {
        this.element = $(element);
        this.settings = $.extend({}, defaults, options);
        this._defaults = defaults;
        this._name = pluginName;
        this.init();
    }

    Plugin.prototype = {
        init: function () {
            var $this = this;
            var $table = this.element;

            // Don't cache ajax requests
            $.ajaxSetup({cache: false});

            $table.css('position', 'relative');

            // Mark inputs as read-only
            $table.find('tr').each(function (i, e) {
                $this.setReadOnly($(e))
                $this.renderDefaultActions($(e));
            });
            this.bindCreateLink();
        },

        setReadOnly: function (row) {
            row.find('input[type="text"]').each(function (i, elem) {
                $(elem).attr('readonly', 'readonly');
            });
        },

        unsetReadOnly: function (row) {
            row.find('input[type="text"]').each(function (i, elem) {
                $(elem).removeAttr('readonly');
            });
        },

        renderDefaultActions: function (row) {
            var $this = this;
            var $row = row;
            var $actions = $row.find('td.actions');

            var html = '<div class="btn-group">';
            if ($this.settings.hasOwnProperty('actions')) {
                for (var action in $this.settings.actions) {
                    var o = $this.settings.actions[action];
                    var url = $this.settings.resourceUrl + '/' + $row.attr('data-id') + '/' + o.resource ;
                    html += '<a href="' + url + '" class="btn btn-default btn-sm">' + o.name + '</a>';
                }
            }
            html += '<a href="#" class="btn btn-default btn-sm dt-edit"><i class="fa fa-fw fa-pencil"></i> Edit</a>' +
            '<a href="#" class="btn btn-default btn-sm dt-delete"><i class="fa fa-fw fa-trash-o"></i> Delete</a>' +
            '</div>';

            $actions.html(html);

            $actions.find('.dt-edit').bind('click', {context: $this}, $this.editClicked);
            $actions.find('.dt-delete').bind('click', {context: $this}, $this.deleteClicked);
        },

        renderCreateActions: function (row) {
            var $this = this;
            var $actions = $(row).find('td.actions');
            $actions.html(
                '<div class="btn-group">' +
                    '<a href="#" class="btn btn-default btn-sm dt-create"><i class="fa fa-fw fa-floppy-o"></i> OK</a>' +
                    '<a href="#" class="btn btn-default btn-sm dt-discard"><i class="fa fa-fw fa-eraser"></i> Cancel</a>' +
                '</div>'
            );

            $actions.find('.dt-create').bind('click', {context: $this}, $this.createClicked);
            $actions.find('.dt-discard').bind('click', {context: $this}, $this.discardClicked);
        },

        renderEditActions: function (row) {
            var $this = this;
            var $actions = $(row).find('td.actions');
            $actions.html(
                '<div class="btn-group">' +
                    '<a href="#" class="btn btn-default btn-sm dt-update"><i class="fa fa-fw fa-check"></i> OK</a>' +
                    '<a href="#" class="btn btn-default btn-sm dt-cancel"><i class="fa fa-fw fa-ban"></i> Cancel</a>' +
                '</div>'
            );

            $actions.find('.dt-update').bind('click', {context: $this}, $this.updateClicked);
            $actions.find('.dt-cancel').bind('click', {context: $this}, $this.cancelClicked);
        },

        bindCreateLink: function (event) {
            var $this = this;
            var $table = this.element;

            $table.siblings('.dt-add').on('click', {context: $this}, $this.addClicked);
        },

        addClicked: function (event) {
            var $this = event.data.context;
            var $table = $this.element;

            $table.spin('teal');
            $.get($this.settings.resourceUrl + '/create', function (data) {
                var $row = $(data).appendTo($table.children('tbody'));
                $this.enablePlugins($row);
                $this.renderCreateActions($row);
                $table.spin(false);
            });
        },

        createClicked: function (event) {
            var $this = event.data.context;
            var $table = $this.element;
            var $row = $(event.target).parents('tr');
            var data = $row.find(':input').serializeObject();

            $table.spin();
            $row.find('input').popover('destroy');
            $.post($this.settings.resourceUrl, data,
                function (response) { // success
                    console.log(response);
                    $row.attr('data-id', response.data.id);
                    $this.getOriginalRow($row);
                },
                'json'
            ).done(function (e) {
                }).fail(function (e) {
                    var message = e.responseJSON.message;
                    console.log(message);
                    for (var key in message) {
                        var $input = $row.find('input[name="' + key + '"]');
                        if ($input.length > 0) {
                            $input.popover({content: message[key].join(), trigger: 'manual', placement: 'bottom'});
                            $input.popover('show');
                        }
                    }
                }).always(function () {
                    $table.spin(false);
                });
        },

        editClicked: function (event) {
            var $this = event.data.context;
            var $table = $this.element;
            var $row = $(event.target).parents('tr');

            $table.spin('teal');
            $.get($this.settings.resourceUrl + '/' + $row.attr('data-id') + '/edit', function (data) {
                var $newRow = $(data).insertAfter($row);
                $this.enablePlugins($newRow);
                $row.remove();
                $this.renderEditActions($newRow);
                $table.spin(false);
            });
        },

        deleteClicked: function (event) {
            var $this = event.data.context;
            var $table = $this.element;
            var $row = $(event.target).parents('tr');

            bootbox.confirm({
                message: 'Are you sure you want to delete this item?',
                title: 'Confirm',
                callback: function(result) {
                    if (result) {
                        var url = $this.settings.resourceUrl + '/' + $row.attr('data-id');
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            success: function(result) {
                                $this.removeRow($row);
                            }
                        }).fail(function (e) {
                            console.log(response);
                        });
                    }
                }
            });
        },

        updateClicked: function (event) {
            var $this = event.data.context;
            var $table = $this.element;
            var $row = $(event.target).parents('tr');
            var url = $this.settings.resourceUrl + '/' + $row.attr('data-id');

            var data = $row.find(':input').serializeObject();
            $table.spin('teal');
            $row.find('input').popover('destroy');
            $.ajax({
                    url: url,
                    type: 'PUT',
                    data: data,
                        dataType: 'json',
                    success: function (response) {
                        console.log(response);
                        $this.getOriginalRow($row);
                    }
                }
            ).done(function (e) {
                }).fail(function (e) {
                    var message = e.responseJSON.message;
                    console.log(message);
                    for (var key in message) {
                        var $input = $row.find('input[name="' + key + '"]');
                        if ($input.length > 0) {
                            $input.popover({content: message[key].join(), trigger: 'manual', placement: 'bottom'});
                            $input.popover('show');
                        }
                    }
                }).always(function () {
                    $table.spin(false);
                });
        },

        cancelClicked: function (event) {
            var $this = event.data.context;
            var $row = $(event.target).parents('tr');

            $this.getOriginalRow($row);
        },

        getOriginalRow: function($row) {
            var $this = this;
            var $table = $this.element;

            $table.spin('teal');
            $.get($this.settings.resourceUrl + '/' + $row.attr('data-id'), function (data) {
                var $newRow = $(data).insertAfter($row);
                $row.remove();

                $this.renderDefaultActions($newRow);
                $table.spin(false);
            });
        },

        discardClicked: function (event) {
            var $this = event.data.context;
            var $row = $(event.target).parents('tr');

            $this.removeRow($row);
        },

        removeRow: function(row) {
            $(row).animate({
                opacity: 0.25,
                left: "+=50",
                height: "toggle",
                backgroundColor: "#FCF8E3"
            }, 250, function () {
                this.remove();
            });
        },

        enablePlugins: function (row) {
            var $row = $(row);

            $row
                .find('select').select2()
                .end()
                .find('.date').datepicker({
                    todayBtn: "linked",
                    autoclose: true,
                    forceParse: true,
                    format: 'yyyy-mm-dd',
                    todayHighlight: true
                })
                .end();
        },

        clear: function () {
            this.element.off("." + pluginName);
            this.element.removeData(pluginName);
        }
    };

    $.fn[pluginName] = function (options) {
        this.each(function () {
            var el = $(this);
            if (el.data(pluginName)) {
                el.data(pluginName).clear();
            }
            el.data(pluginName, new Plugin(this, options));
        });
        return this;
    };

})(jQuery, window, document);