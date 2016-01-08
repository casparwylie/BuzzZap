(function(e) {
    var t = {
        init: function(t) {
            return this.each(function() {
                e(this).data("jSetting", e.extend({}, e.fn.jqFloat.defaults, t));
                e(this).data("jDefined", true);
                var n = e("<div/>").css({
                    width: e(this).outerWidth(true),
                    height: e(this).outerHeight(true),
                    "z-index": e(this).css("zIndex")
                });
                if (e(this).css("position") == "absolute")
                    n.css({
                        position: "absolute",
                        top: e(this).position().top,
                        left: e(this).position().left
                    });
                else 
                    n.css({
                        "float": e(this).css("float"),
                        position: "relative"
                    });
                if ((e(this).css("marginLeft") == "0px" || e(this).css("marginLeft") == "auto") && e(this).position().left > 0 && e(this).css("position") != "absolute") {
                    n.css({
                        marginLeft: e(this).position().left
                    })
                }
                e(this).wrap(n).css({
                    position: "absolute",
                    top: 0,
                    left: 0
                })
            })
        },
        update: function(t) {
            e(this).data("jSetting", e.extend({}, e.fn.jqFloat.defaults, t))
        },
        play: function() {
            if (!e(this).data("jFloating")) {
                e(this).data("jFloating", true)
            }
            n(this)
        },
        stop: function() {
            this.data("jFloating", false)
        }
    };
    var n = function(t) {
        var r = e(t).data("jSetting");
        var i = Math.floor(Math.random() * r.width) - r.width / 2;
        var s = Math.floor(Math.random() * r.height) - r.height / 2 - r.minHeight;
        var o = Math.floor(Math.random() * r.speed) + r.speed / 2;
        e(t).stop().animate({
            top: s,
            left: i
        }, o, function() {
            if (e(this).data("jFloating"))
                n(this);
            else 
                e(this).animate({
                    top: 0,
                    left: 0
                }, o / 2)
        })
    };
    e.fn.jqFloat = function(n, r) {
        var i = e(this);
        if (t[n]) {
            if (i.data("jDefined")) {
                if (r && typeof r === "object")
                    t.update.apply(this, Array.prototype.slice.call(arguments, 1))
                } else 
                    t.init.apply(this, Array.prototype.slice.call(arguments, 1));
            t[n].apply(this)
        } else if (typeof n === "object" ||!n) {
            if (i.data("jDefined")) {
                if (n)
                    t.update.apply(this, arguments)
                } else 
                    t.init.apply(this, arguments);
                    t.play.apply(this)
        } else 
            e.error("Method " + n + " does not exist!");
        return this
    };
    e.fn.jqFloat.defaults = {
        width: 100,
        height: 100,
        speed: 1e3,
        minHeight: 0
    }
})(jQuery)
