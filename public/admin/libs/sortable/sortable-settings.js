document.addEventListener("livewire:initialized", () => {
    const sortableList = document.getElementById("sortable-list");
    if (sortableList) {
        new Sortable(sortableList, {
            animation: 250,
            delay: 50,
            delayOnTouchOnly: true,
            ghostClass: "sortable-ghost",
            chosenClass: "sortable-chosen",
            forceFallback: false,
            onStart: function () {
                document.body.style.cursor = "grabbing";
            },
            onEnd: function (evt) {
                document.body.style.cursor = "default";
                const items = Array.from(sortableList.children).map(
                    (item, index) => ({
                        value: parseInt(item.dataset.id),
                        order: index + 1,
                    })
                );

                // Sayı animasyonu
                sortableList
                    .querySelectorAll(".order-number")
                    .forEach((el, index) => {
                        const oldNumber = parseInt(el.textContent);
                        const newNumber = index + 1;

                        if (oldNumber !== newNumber) {
                            el.classList.add("animate");
                            setTimeout(() => {
                                el.textContent = newNumber;
                            }, 250);
                            setTimeout(() => {
                                el.classList.remove("animate");
                            }, 500);
                        }
                    });

                Livewire.dispatch("updateOrder", { list: items });
            },
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const sortableList = document.getElementById("sortable-list");

    if (sortableList) {
        const sortable = new Sortable(sortableList, {
            animation: 150,
            handle: ".widget-drag-handle",
            ghostClass: "sortable-ghost",
            onEnd: function () {
                const items = [];
                const nodes = sortableList.querySelectorAll(".widget-item");

                nodes.forEach((node, index) => {
                    items.push({
                        value: node.getAttribute("data-id"),
                        order: index + 1,
                    });
                });

                if (window.Livewire) {
                    window.Livewire.dispatch("updateOrder", items);
                }
            },
        });
    }
});
