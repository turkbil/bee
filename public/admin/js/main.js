// Document Ready
$(document).ready(function () {
    // Tooltip başlat
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Dark mode
    var darkModeCookie = Cookies.get("dark");
    if (darkModeCookie && darkModeCookie === "1") {
        $("body")
            .removeClass("light")
            .addClass("dark")
            .attr("data-bs-theme", "dark");
        $(".dark-switch").prop("checked", true);
    } else {
        $("body")
            .removeClass("dark")
            .addClass("light")
            .attr("data-bs-theme", "light");
        $(".dark-switch").prop("checked", false);
    }

    $(".dark-switch")
        .off("change")
        .on("change", function () {
            if ($(this).is(":checked")) {
                $("body")
                    .removeClass("light")
                    .addClass("dark")
                    .attr("data-bs-theme", "dark");
                Cookies.set("dark", "1", { expires: 365 });
            } else {
                $("body")
                    .removeClass("dark")
                    .addClass("light")
                    .attr("data-bs-theme", "light");
                Cookies.set("dark", "0", { expires: 365 });
            }
        });

    const litepickerLocale = {
        months: [
            "Ocak",
            "Şubat",
            "Mart",
            "Nisan",
            "Mayıs",
            "Haziran",
            "Temmuz",
            "Ağustos",
            "Eylül",
            "Ekim",
            "Kasım",
            "Aralık",
        ],
        weekdays: [
            "Pazar",
            "Pazartesi",
            "Salı",
            "Çarşamba",
            "Perşembe",
            "Cuma",
            "Cumartesi",
        ],
        weekdaysShort: ["Paz", "Pzt", "Sal", "Çar", "Per", "Cum", "Cmt"],
    };

    $(".datepicker").each(function () {
        const picker = new Litepicker({
            element: this,
            format: "YYYY-MM-DD",
            singleMode: true,
            dropdowns: {
                minYear: 2000,
                maxYear: new Date().getFullYear(),
                months: true,
                years: true,
            },
            buttonText: {
                previousMonth: `<i class="fa-solid fa-chevron-left"></i>`,
                nextMonth: `<i class="fa-solid fa-chevron-right"></i>`,
            },
            locale: litepickerLocale,
            setup: (picker) => {
                picker.on("selected", (date) => {
                    if ($(this).hasClass("datepicker-start")) {
                        Livewire.emit(
                            "setFilter",
                            "date_start",
                            date.format("YYYY-MM-DD")
                        );
                    } else if ($(this).hasClass("datepicker-end")) {
                        Livewire.emit(
                            "setFilter",
                            "date_end",
                            date.format("YYYY-MM-DD")
                        );
                    }
                });
            },
        });
    });
});

// CSRF Token ayarla
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"),
    },
});

// LIVEWIRE Modal işlemleri
$(document).on("hidden.bs.modal", ".modal", function () {
    $(this)
        .find("form")
        .each(function () {
            this.reset();
        });

    $(this)
        .find(
            'input[type="text"], input[type="email"], input[type="number"], textarea'
        )
        .val("");
    $(this)
        .find('input[type="checkbox"], input[type="radio"]')
        .prop("checked", false);
    $(this).find("select").prop("selectedIndex", 0);
});
