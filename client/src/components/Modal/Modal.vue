<template>
  <div
    class="modal"
    id="modal"
    ref="modal"
    tabindex="-1"
    aria-labelledby="exampleModalLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-dialog-centered" :class="modalSizeClass">
      <div class="modal-content themed-modal">
        <form
          @submit.prevent="onClickYes"
          :class="{ 'was-validated': validated }"
          novalidate
        >
          <div class="modal-header themed-modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">{{ title }}</h1>
            <button
              type="button"
              class="btn-close themed-close"
              @click="
                hide();
                $event.target.blur();
              "
            ></button>
          </div>

          <div class="modal-body themed-modal-body">
            <slot></slot>
          </div>

          <div class="modal-footer themed-modal-footer">
            <button
              type="button"
              class="btn themed-btn-secondary"
              v-if="no"
              @click="
                hide();
                $event.target.blur();
              "
            >
              {{ no }}
            </button>

            <button
              type="submit"
              class="btn themed-btn-primary"
              @click="$event.target.blur()"
            >
              {{ yes }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { Modal } from "bootstrap";

export default {
  emits: ["yesEvent"],
  props: {
    title: { type: String, default: "Modális ablak" },
    yes: { type: String, default: "Mentés" },
    no: { type: String, default: "Mégsem" },
    modalSize: { type: String, default: "" },
  },
  data() {
    return {
      modal: null,
      validated: false,
    };
  },
  mounted() {
    this.modal = new Modal(this.$refs.modal, {
      backdrop: false,
    });
  },
  beforeUnmount() {
    this.cleanupBackdrop();
  },
  computed: {
    modalSizeClass() {
      return {
        "modal-sm": this.modalSize == "sm",
        "modal-lg": this.modalSize == "lg",
        "modal-xl": this.modalSize == "xl",
      };
    },
  },
  methods: {
    onClickYes(event) {
      this.validated = true;

      this.$emit("yesEvent", (success) => {
        if (success) {
          this.hide();
        }
      });
    },
    show() {
      this.modal.show();
      this.applyScrollLock();
      this.validated = false;
    },
    hide() {
      this.modal.hide();
      this.validated = false;
      this.cleanupBackdrop();
    },
    cleanupBackdrop() {
      document.querySelectorAll(".modal-backdrop").forEach((el) => el.remove());
      document.body.classList.remove("modal-open");
      this.clearScrollLock();
    },
    applyScrollLock() {
      const scrollBarWidth = window.innerWidth - document.documentElement.clientWidth;
      document.body.style.overflow = "hidden";
      document.body.style.paddingRight = `${scrollBarWidth}px`;
    },
    clearScrollLock() {
      document.body.style.overflow = "";
      document.body.style.paddingRight = "";
    },
  },
};
</script>

<style scoped>
.themed-modal {
  border: 1px solid rgba(244, 209, 74, 0.55);
  border-radius: 14px;
  background: linear-gradient(180deg, #1a1a1d, #121214);
  color: #f4d14a;
  box-shadow: 0 16px 40px rgba(0, 0, 0, 0.45), 0 0 24px rgba(244, 209, 74, 0.2);
}

.themed-modal-header,
.themed-modal-footer {
  border-color: rgba(244, 209, 74, 0.28);
}

.themed-modal-header .modal-title {
  font-weight: 700;
  letter-spacing: 0.2px;
}

.themed-modal-body {
  color: #eaeaea;
}

.themed-close {
  filter: invert(84%) sepia(49%) saturate(376%) hue-rotate(352deg) brightness(102%) contrast(94%);
}

.themed-btn-primary {
  background: linear-gradient(180deg, #f4d14a, #efc72d);
  border: 1px solid #f4d14a;
  color: #171717;
  font-weight: 700;
}

.themed-btn-primary:hover {
  background: linear-gradient(180deg, #ffe27a, #f4d14a);
  border-color: #ffe27a;
}

.themed-btn-secondary {
  background: rgba(244, 209, 74, 0.12);
  border: 1px solid rgba(244, 209, 74, 0.48);
  color: #f4d14a;
  font-weight: 600;
}

.themed-btn-secondary:hover {
  background: rgba(244, 209, 74, 0.2);
  border-color: #f4d14a;
  color: #ffe680;
}
</style>
