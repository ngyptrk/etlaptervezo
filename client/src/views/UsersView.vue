<template>
  <div>
    <div class="d-flex align-items-center mb-3">
      <h1 class="m-0">Felhasználók</h1>
      <span class="ms-2 text-warning">({{ rows.length }})</span>
    </div>

    <div v-if="loading" class="text-warning fw-semibold">Betöltés...</div>
    <div v-else-if="rows.length === 0" class="empty-list">Nincs találat</div>

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
          <tr v-for="item in rows" :key="item.id">
            <td>{{ item.id }}</td>
            <td>{{ item.name }}</td>
            <td>{{ item.email }}</td>
            <td>{{ roleLabel(item.role) }}</td>
            <td>
              <button class="btn btn-sm btn-outline-info" @click="updateHandler(item)">
                <i class="bi bi-pencil"></i> Módosítás
              </button>
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
  </div>
</template>

<script>
import userService from "@/api/userService";
import FormUser from "@/components/Forms/FormUser.vue";
import { mapState } from "pinia";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";
import { useToastStore } from "@/stores/toastStore";

export default {
  name: "UsersView",
  components: {
    FormUser,
  },
  data() {
    return {
      loading: false,
      rows: [],
      currentItem: { id: 0, name: "", email: "", role: 2 },
      formTitle: "Felhasználó módosítása",
    };
  },
  computed: {
    ...mapState(useUserLoginLogoutStore, ["item", "role"]),
    currentUserId() {
      return this.item?.id ?? 0;
    },
    isEditingOwnAdmin() {
      return this.role === 1 && this.currentItem?.id === this.currentUserId;
    },
  },
  methods: {
    roleLabel(role) {
      if (role === 1) return "Admin";
      if (role === 2) return "Tanár";
      return "Diák";
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
    await this.loadAll();
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

.empty-list {
  border: 1px dashed rgba(244, 209, 74, 0.5);
  border-radius: 12px;
  padding: 1rem;
  color: #f4d14a;
}
</style>
