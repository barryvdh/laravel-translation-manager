<script>
    //https://github.com/rails/jquery-ujs/blob/master/src/rails.js
    @if(config('translation-manager.template') === 'tailwind3')
        $.fn.editableform.buttons = '<button type="submit" class="inline-flex justify-center p-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 editable-submit">' +
        '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></button>' +
        '<button type="button" class="inline-flex justify-center p-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 editable-cancel" style="margin-left: 0">' +
        '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>';
        $.fn.editableform.template = '<form class="form-inline editableform">' +
            '<div class="control-group"><div class="flex"><div class="editable-input"></div><div class="editable-buttons flex flex-col justify-center gap-3"></div></div>' +
            '<div class="editable-error-block"></div>' +
            '</div></form>';
        $.fn.editable.defaults.inputclass = 'shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md';

    @else
        $.fn.editableform.buttons = '<button type="submit" class="btn btn-sm btn-info editable-submit"><i class="fa fa-fw fa-check"></i></button>' +
        '<button type="button" class="btn btn-danger btn-sm editable-cancel"><i class="fa fa-fw fa-remove"></i></button>';
    @endif
    $.fn.editable.defaults.mode = 'inline';

    (function (jquery, t) {
        if (jquery.rails !== t) {
            jquery.error("jquery-ujs has already been loaded!")
        }
        let rails;
        const r = jquery(document);
        jquery.rails = rails = {
            linkClickSelector: "a[data-confirm], a[data-method], a[data-remote], a[data-disable-with]",
            buttonClickSelector: "button[data-remote], button[data-confirm]",
            inputChangeSelector: "select[data-remote], input[data-remote], textarea[data-remote]",
            formSubmitSelector: "form",
            formInputClickSelector: "form input[type=submit], form input[type=image], form button[type=submit], form button:not([type])",
            disableSelector: "input[data-disable-with], button[data-disable-with], textarea[data-disable-with]",
            enableSelector: "input[data-disable-with]:disabled, button[data-disable-with]:disabled, textarea[data-disable-with]:disabled",
            requiredInputSelector: "input[name][required]:not([disabled]),textarea[name][required]:not([disabled])",
            fileInputSelector: "input[type=file]",
            linkDisableSelector: "a[data-disable-with]",
            buttonDisableSelector: "button[data-remote][data-disable-with]",
            CSRFProtection: function (ajax) {
                const token = jquery('meta[name="csrf-token"]').attr('content');
                if (token) ajax.setRequestHeader("X-CSRF-Token", token)
            },
            refreshCSRFTokens: function () {
                const token = jquery('meta[name=csrf-token]').attr('content');
                const tokenParam = jquery('meta[name=csrf-param]').attr('content');
                jquery('form input[name="' + tokenParam + '"]').val(token)
            },
            fire: function (t, event, r) {
                const i = jquery.Event(event);
                t.trigger(i, r);
                return i.result !== false
            },
            confirm: function (e) {
                return confirm(e)
            },
            ajax: function (params) {
                return jquery.ajax(params)
            },
            href: function (e) {
                return e.attr("href")
            },
            handleRemote: function (r) {
                let i, s, o, u, a, f, l, c;
                if (rails.fire(r, "ajax:before")) {
                    u = r.data("cross-domain");
                    a = u === t ? null : u;
                    f = r.data("with-credentials") || null;
                    l = r.data("type") || jquery.ajaxSettings && jquery.ajaxSettings.dataType;
                    if (r.is("form")) {
                        i = r.attr("method");
                        s = r.attr("action");
                        o = r.serializeArray();
                        var h = r.data("ujs:submit-button");
                        if (h) {
                            o.push(h);
                            r.data("ujs:submit-button", null)
                        }
                    } else if (r.is(rails.inputChangeSelector)) {
                        i = r.data("method");
                        s = r.data("url");
                        o = r.serialize();
                        if (r.data("params")) o = o + "&" + r.data("params")
                    } else if (r.is(rails.buttonClickSelector)) {
                        i = r.data("method") || "get";
                        s = r.data("url");
                        o = r.serialize();
                        if (r.data("params")) o = o + "&" + r.data("params")
                    } else {
                        i = r.data("method");
                        s = rails.href(r);
                        o = r.data("params") || null
                    }
                    c = {
                        type: i || "GET", data: o, dataType: l, beforeSend: function (e, i) {
                            if (i.dataType === t) {
                                e.setRequestHeader("accept", "*/*;q=0.5, " + i.accepts.script)
                            }
                            if (rails.fire(r, "ajax:beforeSend", [e, i])) {
                                r.trigger("ajax:send", e)
                            } else {
                                return false
                            }
                        }, success: function (e, t, n) {
                            r.trigger("ajax:success", [e, t, n])
                        }, complete: function (e, t) {
                            r.trigger("ajax:complete", [e, t])
                        }, error: function (e, t, n) {
                            r.trigger("ajax:error", [e, t, n])
                        }, crossDomain: a
                    };
                    if (f) {
                        c.xhrFields = {withCredentials: f}
                    }
                    if (s) {
                        c.url = s
                    }
                    return rails.ajax(c)
                } else {
                    return false
                }
            },
            handleMethod: function (r) {
                let i = rails.href(r), s = r.data('method'), o = r.attr('target'),
                    u = jquery('meta[name=csrf-token]').attr('content'),
                    a = jquery('meta[name=csrf-param]').attr('content'),
                    f = jquery('<form method="post" action="' + i + '"></form>'),
                    l = '<input name="_method" value="' + s + '" type="hidden" />';
                if (a !== t && u !== t) {
                    l += '<input name="' + a + '" value="' + u + '" type="hidden" />'
                }
                if (o) {
                    f.attr("target", o)
                }
                f.hide().append(l).appendTo("body");
                f.submit()
            },
            formElements: function (t, n) {
                return t.is("form") ? jquery(t[0].elements).filter(n) : t.find(n)
            },
            disableFormElements: function (t) {
                rails.formElements(t, rails.disableSelector).each(function () {
                    rails.disableFormElement(jquery(this))
                })
            },
            disableFormElement: function (e) {
                const t = e.is('button') ? 'html' : 'val';
                e.data("ujs:enable-with", e[t]());
                e[t](e.data("disable-with"));
                e.prop("disabled", true)
            },
            enableFormElements: function (t) {
                rails.formElements(t, rails.enableSelector).each(function () {
                    rails.enableFormElement(jquery(this))
                })
            },
            enableFormElement: function (e) {
                var t = e.is("button") ? "html" : "val";
                if (e.data("ujs:enable-with")) e[t](e.data("ujs:enable-with"));
                e.prop("disabled", false)
            },
            allowAction: function (e) {
                var t = e.data("confirm"), r = false, i;
                if (!t) {
                    return true
                }
                if (rails.fire(e, "confirm")) {
                    r = rails.confirm(t);
                    i = rails.fire(e, "confirm:complete", [r])
                }
                return r && i
            },
            blankInputs: function (t, n, r) {
                var i = jquery(), s, o, u = n || "input,textarea", a = t.find(u);
                a.each(function () {
                    s = jquery(this);
                    o = s.is("input[type=checkbox],input[type=radio]") ? s.is(":checked") : s.val();
                    if (!o === !r) {
                        if (s.is("input[type=radio]") && a.filter('input[type=radio]:checked[name="' + s.attr("name") + '"]').length) {
                            return true
                        }
                        i = i.add(s)
                    }
                });
                return i.length ? i : false
            },
            nonBlankInputs: function (e, t) {
                return rails.blankInputs(e, t, true)
            },
            stopEverything: function (t) {
                jquery(t.target).trigger("ujs:everythingStopped");
                t.stopImmediatePropagation();
                return false
            },
            disableElement: function (e) {
                e.data("ujs:enable-with", e.html());
                e.html(e.data("disable-with"));
                e.bind("click.railsDisable", function (e) {
                    return rails.stopEverything(e)
                })
            },
            enableElement: function (element) {
                if (element.data("ujs:enable-with") !== t) {
                    element.html(element.data("ujs:enable-with"));
                    element.removeData("ujs:enable-with")
                }
                element.unbind("click.railsDisable")
            }
        };
        if (rails.fire(r, "rails:attachBindings")) {
            jquery.ajaxPrefilter(function (e, t, r) {
                if (!e.crossDomain) {
                    rails.CSRFProtection(r)
                }
            });
            r.delegate(rails.linkDisableSelector, "ajax:complete", function () {
                rails.enableElement(jquery(this))
            });
            r.delegate(rails.buttonDisableSelector, "ajax:complete", function () {
                rails.enableFormElement(jquery(this))
            });
            r.delegate(rails.linkClickSelector, "click.rails", function (r) {
                var i = jquery(this), s = i.data("method"), o = i.data("params"), u = r.metaKey || r.ctrlKey;
                if (!rails.allowAction(i)) return rails.stopEverything(r);
                if (!u && i.is(rails.linkDisableSelector)) rails.disableElement(i);
                if (i.data("remote") !== t) {
                    if (u && (!s || s === "GET") && !o) {
                        return true
                    }
                    var a = rails.handleRemote(i);
                    if (a === false) {
                        rails.enableElement(i)
                    } else {
                        a.error(function () {
                            rails.enableElement(i)
                        })
                    }
                    return false
                } else if (i.data("method")) {
                    rails.handleMethod(i);
                    return false
                }
            });
            r.delegate(rails.buttonClickSelector, "click.rails", function (t) {
                var r = jquery(this);
                if (!rails.allowAction(r)) return rails.stopEverything(t);
                if (r.is(rails.buttonDisableSelector)) rails.disableFormElement(r);
                var i = rails.handleRemote(r);
                if (i === false) {
                    rails.enableFormElement(r)
                } else {
                    i.error(function () {
                        rails.enableFormElement(r)
                    })
                }
                return false
            });
            r.delegate(rails.inputChangeSelector, "change.rails", function (t) {
                var r = jquery(this);
                if (!rails.allowAction(r)) return rails.stopEverything(t);
                rails.handleRemote(r);
                return false
            });
            r.delegate(rails.formSubmitSelector, "submit.rails", function (r) {
                let i = jquery(this), s = i.data('remote') !== t, o, u;
                if (!rails.allowAction(i)) return rails.stopEverything(r);
                if (i.attr("novalidate") === t) {
                    o = rails.blankInputs(i, rails.requiredInputSelector);
                    if (o && rails.fire(i, "ajax:aborted:required", [o])) {
                        return rails.stopEverything(r)
                    }
                }
                if (s) {
                    u = rails.nonBlankInputs(i, rails.fileInputSelector);
                    if (u) {
                        setTimeout(function () {
                            rails.disableFormElements(i)
                        }, 13);
                        var a = rails.fire(i, "ajax:aborted:file", [u]);
                        if (!a) {
                            setTimeout(function () {
                                rails.enableFormElements(i)
                            }, 13)
                        }
                        return a
                    }
                    rails.handleRemote(i);
                    return false
                } else {
                    setTimeout(function () {
                        rails.disableFormElements(i)
                    }, 13)
                }
            });
            r.delegate(rails.formInputClickSelector, "click.rails", function (t) {
                const r = jquery(this);
                if (!rails.allowAction(r)) return rails.stopEverything(t);
                const i = r.attr('name'), s = i ? {name: i, value: r.val()} : null;
                r.closest("form").data("ujs:submit-button", s)
            });
            r.delegate(rails.formSubmitSelector, "ajax:send.rails", function (t) {
                if (this === t.target) rails.disableFormElements(jquery(this))
            });
            r.delegate(rails.formSubmitSelector, "ajax:complete.rails", function (t) {
                if (this === t.target) rails.enableFormElements(jquery(this))
            });
            jquery(function () {
                rails.refreshCSRFTokens()
            })
        }
    })(jQuery)
</script>

<script>
    jQuery(document).ready(function ($) {
        $.ajaxSetup({
            beforeSend: function (xhr, settings) {
                console.log('beforesend');
                settings.data += "&_token={{csrf_token()}}";
            }
        });

        $('.editable').editable().on('hidden', function (e, reason) {
            const locale = $(this).data('locale');
            if (reason === 'save') {
                $(this).removeClass('status-0').addClass('font-weight-bold');
            }
            if (reason === 'save' || reason === 'nochange') {
                const $next = $(this).closest('tr').next().find('.editable.locale-' + locale);
                setTimeout(function () {
                    $next.editable('show');
                }, 300);
            }
        });

        $('.group-select').on('change', function () {
            const group = $(this).val();
            if (group) {
                window.location.href = '{{ action($controller . '@getView') }}/' + $(this).val();
            } else {
                window.location.href = '{{ action($controller . '@getIndex') }}';
            }
        });

        $("a.delete-key").click(function (event) {
            event.preventDefault();
            const row = $(this).closest('tr');
            const url = $(this).attr('href');
            const id = row.attr('id');
            $.post(url, {id: id}, function () {
                row.remove();
            });
        });

        $('.form-import').on('ajax:success', function (e, data) {
            $('div.success-import strong.counter').text(data.counter);
            $('div.success-import').slideDown();
            window.location.reload();
        });

        $('.form-find').on('ajax:success', function (e, data) {
            $('div.success-find strong.counter').text(data.counter);
            $('div.success-find').slideDown();
            window.location.reload();
        });

        $('.form-publish').on('ajax:success', function (e, data) {
            $('div.success-publish').slideDown();
        });

        $('.form-publish-all').on('ajax:success', function (e, data) {
            $('div.success-publish-all').slideDown();
        });
    });
</script>
