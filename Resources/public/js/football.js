var APP = APP || {};

(function ($) {

    if (typeof APP === 'object') {

        APP.football = function (width, height, options) {
            var self = this;

            self.width = width;
            self.height = height;

            self.xhr = null;

            self.points = {};

            var defaults = {
                url: null,
                wrapperSelector: '#football',
                pointSelector: '.point',
                pointLineClass: 'point-line'
            };

            self.settings = $.extend({}, defaults, options);

            // ~

            self.getDirection = function (start, end) {
                if (start instanceof APP.footballPoint === false || end instanceof APP.footballPoint === false) {
                    throw new APP.footballException('Required APP.footballPoint object');
                }
                var w = end.x - start.x;
                var h = end.y - start.y;

                switch (true) {
                    case ((w === -1) && (h === -1)):
                        return 0;
                        break;
                    case ((w === 0) && (h === -1)):
                        return 1;
                        break;
                    case ((w === 1) && (h === -1)):
                        return 2;
                        break;
                    case ((w === 1) && (h === 0)):
                        return 3;
                        break;
                    case ((w === 1) && (h === 1)):
                        return 4;
                        break;
                    case ((w === 0) && (h === 1)):
                        return 5;
                        break;
                    case ((w === -1) && (h === 1)):
                        return 6;
                        break;
                    case ((w === -1) && (h === 0)):
                        return 7;
                        break;
                    default:
                        throw new APP.footballException('Not possible move: ' + w + '_' + h);
                }
            };

            // ~

            self.getBoardCenter = function () {
                return new APP.footballPoint(Math.floor(self.width / 2), Math.floor(self.height / 2));
            };

            // ~

            self.getLastPoint = function () {
                if (Object.keys(self.points).length) {
                    var point = self.points[Object.keys(self.points).length - 1];
                    return new APP.footballPoint(point.x, point.y);
                }

                return self.getBoardCenter();
            };

            // ~

            self.pointListener = function () {
                $('body').delegate(self.settings.pointSelector, 'click', function (e) {
                    var start = self.getLastPoint();
                    var end = new APP.footballPoint($(this).data('x'), $(this).data('y'));
                    try {
                        var dir = self.getDirection(start, end);
                        self.sendRequest(end);
                    } catch (e) {
                        self.message(e.message, 'warning');
                    }
                });
            };

            // ~

            self.rebuildBoard = function (html) {
                if ($(self.settings.wrapperSelector).length) {
                    $(self.settings.wrapperSelector).html(html);
                }
            };

            // ~

            self.drawLine = function (start, dir) {
                if (start instanceof APP.footballPoint === false) {
                    throw new APP.footballException('Required APP.footballPoint object');
                }

                var htmlPoint = $(self.settings.pointSelector + "[data-x='" + start.getX() + "'][data-y='" + start.getY() + "']");
                if (htmlPoint) {
                    htmlPoint.append('<span class="' + self.settings.pointLineClass + '" data-deg="' + dir + '"></span>');
                }
            };

            // ~

            self.drawLines = function () {
                $.each(self.points, function (key) {
                    if (typeof self.points[key] === 'object' && typeof self.points[key + 1] === 'object') {
                        try {
                            var start = new APP.footballPoint(self.points[key].x, self.points[key].y);
                            var end = new APP.footballPoint(self.points[key + 1].x, self.points[key + 1].y);
                            var dir = self.getDirection(start, end);
                            self.drawLine(start, dir);
                        } catch (e) {

                        }
                    }
                });
            };

            // ~

            self.sendRequest = function (end) {

                if (self.xhr && self.xhr.readyState !== 4) {
                    self.xhr.abort();
                }

                self.xhr = $.ajax({
                    cache: false,
                    url: self.settings.url,
                    method: 'GET',
                    dataType: 'json',
                    async: false,
                    data: {
                        width: self.width,
                        height: self.height,
                        x: end.x,
                        y: end.y
                    },
                    beforeSend: function () {

                    },
                    complete: function () {

                    },
                    error: function (request, status, error) {

                    },
                    success: function (response, status, request) {
                        self.points = response.points;
                        self.rebuildBoard(response.template);
                        self.drawLines();
                        self.message('reflection:' + response.reflection + ' currentuser:' + response.currentuser);
                        if (typeof response.error === 'object') {
                            self.message(response.error.message, 'warning');
                        }
                    }
                });
            };

            // ~

            self.init = function () {
                self.pointListener();
            };

            self.init();

        };

        APP.football.prototype.message = function (message, type) {
            if (typeof APP.growl === 'function') {
                var type = typeof type === 'string' ? type : 'notice';
                new APP.growl('.growl').add(message, type, 5000);
            }
        };

        APP.footballPoint = function (x, y) {
            var self = this;

            self.x = x;
            self.y = y;

            self.getX = function () {
                return parseInt(self.x);
            };

            self.getY = function () {
                return parseInt(self.y);
            };

            return this;
        };

        APP.footballException = function (message) {
            this.message = message;
            this.name = "footballException";
        };

    }

})(jQuery);