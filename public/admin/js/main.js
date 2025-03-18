// Tom Select Konfigürasyonu
const tomSelectConfig = {
    plugins: {
        remove_button: {
            title: "Sil",
        },
        restore_on_backspace: {},
    },
    persist: false,
    create: true,
    createOnBlur: true,
    delimiter: ",",
    render: {
        no_results: function () {
            return "";
        },
    }
};

// Tom Select'i başlat
function initializeTomSelect() {
    const elements = document.querySelectorAll(".tags, .tomselect, .tom-select");
    if (!elements.length) return;

    elements.forEach(function (input) {
        if (!input || !input.tagName || input.tagName.toLowerCase() !== 'select') return;

        try {
            if (input.tomselect) {
                input.tomselect.destroy();
            }
            new TomSelect(input, tomSelectConfig);
        } catch (e) {
            console.error("Tom Select başlatma hatası:", e);
        }
    });
}

// Document Ready
document.addEventListener("DOMContentLoaded", function () {
    // Tom Select'i başlat
    initializeTomSelect();

    // Livewire sayfa yüklendiğinde Tom Select'i yeniden başlat
    document.addEventListener("livewire:navigated", function () {
        window.requestAnimationFrame(initializeTomSelect);
    });

    // Tooltip başlat
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    if (tooltips.length) {
        tooltips.forEach(function (tooltip) {
            new bootstrap.Tooltip(tooltip);
        });
    }

    // Dark mode
    const darkModeCookie = Cookies.get("dark");
    const body = document.body;
    const darkSwitches = document.querySelectorAll(".dark-switch");

    if (darkModeCookie === "1") {
        body.classList.remove("light");
        body.classList.add("dark");
        body.setAttribute("data-bs-theme", "dark");
        darkSwitches.forEach(function(switchEl) {
            switchEl.checked = true;
        });
    } else {
        body.classList.remove("dark");
        body.classList.add("light");
        body.setAttribute("data-bs-theme", "light");
        darkSwitches.forEach(function(switchEl) {
            switchEl.checked = false;
        });
    }

    darkSwitches.forEach(function (switchEl) {
        switchEl.addEventListener("change", function () {
            if (switchEl.checked) {
                body.classList.remove("light");
                body.classList.add("dark");
                body.setAttribute("data-bs-theme", "dark");
                Cookies.set("dark", "1", { expires: 365 });
            } else {
                body.classList.remove("dark");
                body.classList.add("light");
                body.setAttribute("data-bs-theme", "light");
                Cookies.set("dark", "0", { expires: 365 });
            }
        });
    });

    // Module menu
    const dropdowns = document.querySelectorAll(".module-menu .dropdown");
    dropdowns.forEach(function (dropdown) {
        dropdown.addEventListener("click", function (event) {
            dropdown.classList.add("open");
            event.stopPropagation();
        });
    });

    document.addEventListener("click", function (e) {
        if (!e.target.closest(".module-menu .dropdown")) {
            dropdowns.forEach(function(dropdown) {
                dropdown.classList.remove("open");
            });
        }
    });

    const moduleItems = document.querySelectorAll(".module-menu .dropdown-module-item");
    moduleItems.forEach(function (item) {
        item.addEventListener("click", function (e) {
            e.stopPropagation();
        });
    });

    // Datepicker
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
        weekdaysShort: ["Paz", "Pzt", "Sal", "Çar", "Per", "Cum", "Cmt"],
    };

    const datepickers = document.querySelectorAll(".datepicker");
    datepickers.forEach(function (datepicker) {
        const picker = new Litepicker({
            element: datepicker,
            format: "YYYY-MM-DD",
            singleMode: true,
            dropdowns: {
                months: true,
                years: true,
            },
            numberOfMonths: 1,
            numberOfColumns: 1,
            resetButton: true,
            lang: "tr-TR",
            locale: litepickerLocale,
            setup: function (picker) {
                picker.on("selected", function (date) {
                    if (datepicker.classList.contains("datepicker-start")) {
                        // Livewire.emit("setFilter", "date_start", date.format("YYYY-MM-DD"));
                    } else if (datepicker.classList.contains("datepicker-end")) {
                        // Livewire.emit("setFilter", "date_end", date.format("YYYY-MM-DD"));
                    }
                });
            },
        });
    });
});

// CSRF Token ayarla
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute("content"),
    },
});

// Modal kapatıldığında formu sıfırla
document.addEventListener("hidden.bs.modal", function (event) {
    const modal = event.target;
    if (!modal) return;

    const forms = modal.querySelectorAll("form");
    forms.forEach(function(form) {
        form.reset();
    });

    const inputs = modal.querySelectorAll('input[type="text"], input[type="email"], input[type="number"], textarea');
    inputs.forEach(function(input) {
        input.value = "";
    });

    const checkboxes = modal.querySelectorAll('input[type="checkbox"], input[type="radio"]');
    checkboxes.forEach(function(input) {
        input.checked = false;
    });

    const selects = modal.querySelectorAll("select");
    selects.forEach(function(select) {
        select.selectedIndex = 0;
    });
});
