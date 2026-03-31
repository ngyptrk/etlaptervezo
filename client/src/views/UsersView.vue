<template>
  <div>
    <div class="d-flex align-items-center justify-content-between mb-3 gap-3 flex-wrap">
      <div class="d-flex align-items-center">
        <h1 class="m-0">Felhasználók</h1>
        <span class="ms-2 text-warning">({{ filteredRows.length }})</span>
      </div>
      <div class="search-wrap">
        <i class="bi bi-search search-icon"></i>
        <input
          v-model="searchWordInput"
          type="text"
          class="form-control search-input"
          placeholder="Keresés név vagy email alapján..."
        />
      </div>
    </div>

    <div v-if="loading" class="text-warning fw-semibold">Betöltés...</div>
    <div v-else-if="filteredRows.length === 0" class="empty-list">Nincs találat</div>

    <div v-else class="list-wrap table-responsive">
      <table class="table list-table m-0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Név</th>
            <th>Email</th>
            <th>Szerepkör</th>
            <th>Művelet</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in filteredRows" :key="item.id">
            <td>{{ item.id }}</td>
            <td>
              <div>{{ item.name }}</div>
              <div v-if="deleteErrors[item.id]" class="delete-inline-error">{{ deleteErrors[item.id] }}</div>
            </td>
            <td>{{ item.email }}</td>
            <td>{{ roleLabel(item.role) }}</td>
            <td>
              <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-info" @click="updateHandler(item)">
                  <i class="bi bi-pencil"></i> Módosítás
                </button>
                <button
                  v-if="canDeleteUser(item)"
                  class="btn btn-sm btn-outline-danger"
                  @click="deleteHandler(item)"
                >
                  <i class="bi bi-trash"></i> Törlés
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <FormUser
      ref="form"
      :title="formTitle"
      :item="currentItem"
      :disable-role="isEditingOwnAdmin"
      @yesEventForm="yesEventFormHandler"
    />

    <ConfirmModal
      :isOpenConfirmModal="isOpenConfirmModal"
      :title="confirmTitle"
      :message="confirmMessage"
      cancel="Mégsem"
      confirm="Igen"
      @cancel="closeConfirmModal"
      @confirm="confirmActionHandler"
    />
  </div>
</template>

<script>
import userService from "@/api/userService";
import FormUser from "@/components/Forms/FormUser.vue";
import ConfirmModal from "@/components/Confirm/ConfirmModal.vue";
import { mapActions, mapState } from "pinia";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";
import { useToastStore } from "@/stores/toastStore";
import { useSearchStore } from "@/stores/searchStore";

export default {
  name: "UsersView",
  components: {
    FormUser,
    ConfirmModal,
  },
  data() {
    return {
      loading: false,
      rows: [],
      currentItem: { id: 0, name: "", email: "", role: 2 },
      formTitle: "Felhasználó módosítása",
      isOpenConfirmModal: false,
      confirmTitle: "",
      confirmMessage: "",
      confirmAction: null,
      deleteErrors: {},
      pendingDeleteId: null,
    };
  },
  computed: {
    ...mapState(useUserLoginLogoutStore, ["item", "role"]),
    ...mapState(useSearchStore, ["searchword", "searchWord"]),
    currentUserId() {
      return this.item?.id ?? 0;
    },
    isEditingOwnAdmin() {
      return this.role === 1 && this.currentItem?.id === this.currentUserId;
    },
    searchWordInput: {
      get() {
        return this.searchWord;
      },
      set(value) {
        this.setSearchWord(value);
      },
    },
    filteredRows() {
      if (!this.searchword) return this.rows;
      return this.rows.filter((item) =>
        [item.name, item.email, String(item.id), this.roleLabel(item.role)]
          .join(" ")
          .toLowerCase()
          .includes(this.searchword),
      );
    },
  },
  methods: {
    ...mapActions(useSearchStore, ["setSearchWord", "resetSearchWord"]),
    resetDeleteState() {
      this.pendingDeleteId = null;
      this.confirmTitle = "";
      this.confirmMessage = "";
      this.confirmAction = null;
    },
    openConfirmModal({ title, message, onConfirm }) {
      this.confirmTitle = title;
      this.confirmMessage = message;
      this.confirmAction = onConfirm;
      this.isOpenConfirmModal = true;
    },
    closeConfirmModal() {
      this.isOpenConfirmModal = false;
      this.resetDeleteState();
    },
    async confirmActionHandler() {
      const action = this.confirmAction;
      if (typeof action === "function") {
        await action();
      }
      this.closeConfirmModal();
    },
    roleLabel(role) {
      if (role === 1) return "Admin";
      if (role === 2 || role === 3) return "Felhasználó";
      return "Ismeretlen";
    },
    canDeleteUser(item) {
      if (this.role !== 1) return false;
      if (!item || item.id === this.currentUserId) return false;
      return item.role !== 1;
    },
    async loadAll() {
      this.loading = true;
      try {
        const response = await userService.getAll();
        this.rows = response.data ?? [];
      } finally {
        this.loading = false;
      }
    },
    updateHandler(item) {
      this.currentItem = { ...item };
      this.$refs.form.show();
    },
    deleteHandler(item) {
      this.deleteErrors[item.id] = "";
      this.pendingDeleteId = item.id;
      this.openConfirmModal({
        title: "Törlés megerősítése",
        message: `Biztosan törölni szeretnéd ezt a felhasználót: "${item.name}"?`,
        onConfirm: async () => {
          try {
            const id = this.pendingDeleteId;
            this.resetDeleteState();
            if (!id) return;
            const res = await userService.delete(id);
            const isRestricted = res?.restricted || res?.data?.restricted;
            const restrictedMessage = res?.data?.message || res?.message;
            if (isRestricted) {
              this.deleteErrors[item.id] = restrictedMessage || "Sikertelen törlés";
              return;
            }
            await this.loadAll();
          } catch (err) {
            const message = err?.response?.data?.message || "Sikertelen törlés";
            this.deleteErrors[item.id] = message;
          }
        },
      });
    },
    async yesEventFormHandler({ item, done }) {
      try {
        const payload = { ...item };

        if (this.role === 1 && payload.id === this.currentUserId && payload.role !== this.role) {
          payload.role = this.role;
          useToastStore().messages.push("Adminként a saját szerepköröd nem módosítható.");
          useToastStore().show("Error");
        }

        await userService.update(payload.id, payload);
        await this.loadAll();
        done(true);
      } catch (err) {
        if (err.response && err.response.status === 422) {
          this.$refs.form.setServerErrors(err.response.data.errors ?? {});
        }
        done(false);
      }
    },
  },
  async mounted() {
    this.resetSearchWord();
    await this.loadAll();
  },
  beforeUnmount() {
    this.resetSearchWord();
  },
};
</script>

<style scoped>
.list-wrap {
  border: 1px solid rgba(244, 209, 74, 0.35);
  border-radius: 12px;
  overflow: hidden;
}

.list-table thead th {
  background: #1e2229;
  color: #ffd84f;
  border-bottom: 1px solid #ffd84f;
}

.list-table tbody td {
  background: #d6d6d8;
  color: #141414;
  border-color: #b9b9bc;
}

.list-table tbody tr:nth-child(even) td {
  background: #cfcfd2;
}

.search-wrap {
  position: relative;
  min-width: 320px;
}

.search-icon {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  color: #8a8a8a;
}

.search-input {
  padding-left: 34px;
  border: 1px solid rgba(244, 209, 74, 0.45);
  background: #101216;
  color: #f1f1f1;
}

.search-input:focus {
  border-color: #f4d14a;
  box-shadow: 0 0 0 0.2rem rgba(244, 209, 74, 0.2);
  background: #101216;
  color: #f1f1f1;
}

.empty-list {
  border: 1px dashed rgba(244, 209, 74, 0.5);
  border-radius: 12px;
  padding: 1rem;
  color: #f4d14a;
}
</style>
