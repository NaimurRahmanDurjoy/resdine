export default class CrudManager {
    constructor(wrapperSelector) {
        this.wrapper = document.querySelector(wrapperSelector);
        if (!this.wrapper) return;

        this.baseRoute = this.wrapper.dataset.baseRoute;
        this.tableContainer = document.querySelector("#table-container");
        this.modal = document.querySelector("#global-modal");
        this.modalBody = document.querySelector("#modal-body");

        this.routes = {
            index: `${this.baseRoute}`,
            create: `${this.baseRoute}/create`,
            edit: (id) => `${this.baseRoute}/${id}/edit`,
            show: (id) => `${this.baseRoute}/${id}`,
            destroy: (id) => `${this.baseRoute}/${id}`,
        };

        this.initListeners();
    }

    /** Fetch content via AJAX and update a container */
    async fetchTable(url = this.routes.index) {
        const res = await fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } });
        const html = await res.text();
        this.tableContainer.innerHTML = html;
    }

    /** Open content inside modal */
    async openModal(url) {
        const res = await fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } });
        const html = await res.text();
        this.modalBody.innerHTML = html;
        this.modal.classList.remove("hidden");
        this.modal.classList.add("flex");
    }

    closeModal() {
        this.modal.classList.add("hidden");
        this.modalBody.innerHTML = "";
    }

    initListeners() {
        // Close modal on overlay or button click
        this.modal?.addEventListener("click", (e) => {
            if (e.target.classList.contains("close-modal") || e.target === this.modal) {
                this.closeModal();
            }
        });

        // Click handler for CRUD buttons
        this.wrapper.addEventListener("click", async (e) => {
            const target = e.target.closest("a, button");
            if (!target) return;

            const viewType = target.getAttribute("view"); // optional "modal"
            const id = target.dataset.id;

            // Pagination, sorting
            if (target.href && (target.href.includes("page=") || target.href.includes("sort="))) {
                e.preventDefault();
                return this.fetchTable(target.href);
            }

            // Add new
            if (target.classList.contains("add-new")) {
                e.preventDefault();
                const url = this.routes.create;
                return viewType === "modal" ? this.openModal(url) : this.fetchTable(url);
            }

            // Edit
            if (target.classList.contains("edit-btn")) {
                e.preventDefault();
                const url = this.routes.edit(id);
                return viewType === "modal" ? this.openModal(url) : this.fetchTable(url);
            }

            // View
            if (target.classList.contains("go-btn")) {
                e.preventDefault();
                const url = this.routes.show(id);
                return viewType === "modal" ? this.openModal(url) : this.fetchTable(url);
            }

            // Delete
            if (target.classList.contains("delete-btn")) {
                e.preventDefault();
                if (!confirm("Are you sure you want to delete this record?")) return;

                const res = await fetch(this.routes.destroy(id), {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: new URLSearchParams({ _method: "DELETE" }),
                });

                if (res.ok) {
                    this.fetchTable(); // refresh table after delete
                }
            }
        });

        // AJAX form submit
        document.body.addEventListener("submit", async (e) => {
            const form = e.target.closest("form");
            if (!form) return;
            e.preventDefault();

            const data = new FormData(form);
            const res = await fetch(form.action, {
                method: form.method,
                headers: { "X-Requested-With": "XMLHttpRequest" },
                body: data,
            });

            if (res.ok) {
                this.closeModal();
                this.fetchTable(); // refresh table after create/update
            } else {
                const html = await res.text();
                this.modalBody.innerHTML = html; // show validation errors
            }
        });
    }
}
