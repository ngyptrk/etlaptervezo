<template>
  <div>
    <div class="d-flex align-items-center justify-content-between mb-3 gap-3 flex-wrap">
      <div class="d-flex align-items-center"><h1 class="m-0">Mértékegységek</h1><span class="ms-2 text-warning">({{ filteredRows.length }})</span></div>
      <div class="search-wrap"><i class="bi bi-search search-icon"></i><input v-model="searchWordInput" type="text" class="form-control search-input" placeholder="Keresés mértékegységre..." /></div>
      <button class="btn btn-success btn-sm" @click="createHandler"><i class="bi bi-plus-lg"></i> Hozzáadás</button>
    </div>
    <div v-if="loading" class="text-warning fw-semibold">Betöltés...</div>
    <div v-else-if="filteredRows.length === 0" class="empty-list">Nincs találat</div>
    <div v-else class="list-wrap table-responsive">
      <table class="table list-table m-0"><thead><tr><th>ID</th><th>Mértékegység</th><th>Művelet</th></tr></thead><tbody>
        <tr v-for="item in filteredRows" :key="item.id"><td>{{ item.id }}</td><td>{{ item.unit }}</td><td><div class="d-flex gap-2"><button class="btn btn-sm btn-outline-info" @click="updateHandler(item)"><i class="bi bi-pencil"></i> Módosítás</button><button class="btn btn-sm btn-outline-danger" @click="deleteHandler(item)"><i class="bi bi-trash"></i> Törlés</button></div></td></tr>
      </tbody></table>
    </div>
    <FormUnit ref="form" :title="formTitle" :item="currentItem" @yesEventForm="yesEventFormHandler" />
    <ConfirmModal :isOpenConfirmModal="isOpenConfirmModal" :title="confirmTitle" :message="confirmMessage" cancel="Mégsem" confirm="Igen" @cancel="closeConfirmModal" @confirm="confirmActionHandler" />
  </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import unitService from "@/api/unitService";
import FormUnit from "@/components/Forms/FormUnit.vue";
import ConfirmModal from "@/components/Confirm/ConfirmModal.vue";
import { useSearchStore } from "@/stores/searchStore";

export default {
  name: "UnitView",
  components: { FormUnit, ConfirmModal },
  data() { return { loading: false, rows: [], mode: "create", currentItem: { id: 0, unit: "" }, formTitle: "Új mértékegység", isOpenConfirmModal: false, confirmTitle: "", confirmMessage: "", confirmAction: null }; },
  computed: {
    ...mapState(useSearchStore, ["searchword", "searchWord"]),
    searchWordInput: { get() { return this.searchWord; }, set(value) { this.setSearchWord(value); } },
    filteredRows() { if (!this.searchword) return this.rows; return this.rows.filter((item) => [String(item.id), item.unit].join(" ").toLowerCase().includes(this.searchword)); },
  },
  methods: {
    ...mapActions(useSearchStore, ["setSearchWord", "resetSearchWord"]),
    openConfirmModal({ title, message, onConfirm }) { this.confirmTitle = title; this.confirmMessage = message; this.confirmAction = onConfirm; this.isOpenConfirmModal = true; },
    closeConfirmModal() { this.isOpenConfirmModal = false; this.confirmTitle = ""; this.confirmMessage = ""; this.confirmAction = null; },
    async confirmActionHandler() { const action = this.confirmAction; this.closeConfirmModal(); if (typeof action === "function") await action(); },
    async loadAll() { this.loading = true; try { const response = await unitService.getAll(); this.rows = response.data ?? []; } finally { this.loading = false; } },
    updateHandler(item) { this.openConfirmModal({ title: "Módosítás megerősítése", message: `Biztosan módosítani szeretnéd ezt a mértékegységet: "${item.unit}"?`, onConfirm: () => this.startUpdate(item) }); },
    startUpdate(item) { this.mode = "update"; this.formTitle = "Mértékegység módosítása"; this.currentItem = { ...item }; this.$refs.form.show(); },
    createHandler() { this.mode = "create"; this.formTitle = "Új mértékegység"; this.currentItem = { id: 0, unit: "" }; this.$refs.form.show(); },
    deleteHandler(item) { this.openConfirmModal({ title: "Törlés megerősítése", message: `Biztosan törölni szeretnéd ezt a mértékegységet: "${item.unit}"?`, onConfirm: async () => { await unitService.delete(item.id); await this.loadAll(); } }); },
    async yesEventFormHandler({ item, done }) { try { if (this.mode === "create") await unitService.create(item); else await unitService.update(item.id, item); await this.loadAll(); done(true); } catch (err) { if (err.response && err.response.status === 422) this.$refs.form.setServerErrors(err.response.data.errors); done(false); } },
  },
  async mounted() { this.resetSearchWord(); await this.loadAll(); },
  beforeUnmount() { this.resetSearchWord(); },
};
</script>

<style scoped>
.list-wrap { border: 1px solid rgba(244, 209, 74, 0.35); border-radius: 12px; overflow: hidden; }
.list-table thead th { background: #1e2229; color: #ffd84f; border-bottom: 1px solid #ffd84f; }
.list-table tbody td { background: #d6d6d8; color: #141414; border-color: #b9b9bc; }
.list-table tbody tr:nth-child(even) td { background: #cfcfd2; }
.search-wrap { position: relative; min-width: 300px; }
.search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #8a8a8a; }
.search-input { padding-left: 34px; border: 1px solid rgba(244, 209, 74, 0.45); background: #101216; color: #f1f1f1; }
.search-input:focus { border-color: #f4d14a; box-shadow: 0 0 0 0.2rem rgba(244, 209, 74, 0.2); background: #101216; color: #f1f1f1; }
.empty-list { border: 1px dashed rgba(244, 209, 74, 0.5); border-radius: 12px; padding: 1rem; color: #f4d14a; }
</style>
