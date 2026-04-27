<template>
  <div>
    <div class="d-flex align-items-center justify-content-between mb-3 gap-3 flex-wrap">
      <div class="d-flex align-items-center">
        <h1 class="m-0">Hozzávalók</h1>
        <span class="ms-2 text-warning">({{ filteredRows.length }})</span>
      </div>

      <div class="search-wrap">
        <i class="bi bi-search search-icon"></i>
        <input
          v-model="searchWordInput"
          type="text"
          class="form-control search-input"
          placeholder="Keresés hozzávalóra..."
        />
      </div>

      <button class="btn btn-success btn-sm" @click="createHandler">
        <i class="bi bi-plus-lg"></i> Hozzáadás
      </button>
    </div>

    <div v-if="loading" class="text-warning fw-semibold">Betöltés...</div>
    <div v-else-if="filteredRows.length === 0" class="empty-list">Nincs találat</div>

    <div v-else class="list-wrap table-responsive">
      <table class="table list-table m-0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Recept</th>
            <th>Nyers hozzávaló</th>
            <th>Mennyiség</th>
            <th>Mértékegység</th>
            <th class="action-header">Művelet</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in filteredRows" :key="item.id">
            <td>{{ item.id }}</td>
            <td>{{ recipeName(item.recipe_id) }}</td>
            <td class="ingredient-cell">
              <span>{{ rawIngredientName(item.raw_ingredient_id) }}</span>
              <span v-if="deleteErrors[item.id]" class="delete-inline-error">
                - {{ deleteErrors[item.id] }}
              </span>
            </td>
            <td>{{ item.amount }}</td>
            <td>{{ unitName(item.unit_id) }}</td>
            <td class="action-cell">
              <div class="d-flex justify-content-end gap-2 flex-nowrap">
                <button class="btn btn-sm btn-outline-info" @click="updateHandler(item)">
                  <i class="bi bi-pencil"></i> Módosítás
                </button>
                <button class="btn btn-sm btn-outline-danger" @click="deleteHandler(item)">
                  <i class="bi bi-trash"></i> Törlés
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <FormIngredient
      ref="form"
      :title="formTitle"
      :item="currentItem"
      :recipes="recipes"
      :rawIngredients="rawIngredients"
      :units="units"
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
import { mapActions, mapState } from "pinia";
import ingredientService from "@/api/ingredientService";
import recipeService from "@/api/recipeService";
import rawIngredientService from "@/api/rawIngredientService";
import unitService from "@/api/unitService";
import FormIngredient from "@/components/Forms/FormIngredient.vue";
import ConfirmModal from "@/components/Confirm/ConfirmModal.vue";
import { useSearchStore } from "@/stores/searchStore";

export default {
  name: "IngredientView",
  components: { FormIngredient, ConfirmModal },
  data() {
    return {
      loading: false,
      rows: [],
      recipes: [],
      rawIngredients: [],
      units: [],
      mode: "create",
      currentItem: { id: 0, recipe_id: 0, raw_ingredient_id: 0, amount: 1, unit_id: 0 },
      formTitle: "Új hozzávaló",
      isOpenConfirmModal: false,
      confirmTitle: "",
      confirmMessage: "",
      confirmAction: null,
      deleteErrors: {},
      pendingDeleteId: null,
    };
  },
  computed: {
    ...mapState(useSearchStore, ["searchword", "searchWord"]),
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
        [
          String(item.id),
          this.recipeName(item.recipe_id),
          this.rawIngredientName(item.raw_ingredient_id),
          String(item.amount),
          this.unitName(item.unit_id),
        ]
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
      if (typeof action === "function") await action();
      this.closeConfirmModal();
    },
    recipeName(id) {
      return this.recipes.find((x) => x.id === id)?.name ?? `#${id}`;
    },
    rawIngredientName(id) {
      return this.rawIngredients.find((x) => x.id === id)?.raw_ingredient ?? `#${id}`;
    },
    unitName(id) {
      return this.units.find((x) => x.id === id)?.unit ?? `#${id}`;
    },
    async loadAll() {
      this.loading = true;
      try {
        const [ingredientsRes, recipesRes, rawRes, unitRes] = await Promise.all([
          ingredientService.getAll(),
          recipeService.getAll(),
          rawIngredientService.getAll(),
          unitService.getAll(),
        ]);
        this.rows = ingredientsRes.data ?? [];
        this.recipes = recipesRes.data ?? [];
        this.rawIngredients = rawRes.data ?? [];
        this.units = unitRes.data ?? [];
      } finally {
        this.loading = false;
      }
    },
    createHandler() {
      this.mode = "create";
      this.formTitle = "Új hozzávaló";
      this.currentItem = {
        id: 0,
        recipe_id: this.recipes[0]?.id ?? 0,
        raw_ingredient_id: this.rawIngredients[0]?.id ?? 0,
        amount: 1,
        unit_id: this.units[0]?.id ?? 0,
      };
      this.$refs.form.show();
    },
    updateHandler(item) {
      this.startUpdate(item);
    },
    startUpdate(item) {
      this.mode = "update";
      this.formTitle = "Hozzávaló módosítás";
      this.currentItem = { ...item };
      this.$refs.form.show();
    },
    deleteHandler(item) {
      this.deleteErrors[item.id] = "";
      this.pendingDeleteId = item.id;
      const ingredientLabel = this.rawIngredientName(item.raw_ingredient_id);
      this.openConfirmModal({
        title: "Törlés megerősítése",
        message: `Biztosan törölni szeretnéd ezt a hozzávalót: "${ingredientLabel}"?`,
        onConfirm: async () => {
          try {
            const id = this.pendingDeleteId;
            this.resetDeleteState();
            if (!id) return;
            const res = await ingredientService.delete(id);
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
        if (this.mode === "create") await ingredientService.create(item);
        else await ingredientService.update(item.id, item);
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
  overflow-x: auto;
  overflow-y: hidden;
  -webkit-overflow-scrolling: touch;
}

.list-table {
  table-layout: fixed;
  width: 100%;
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

.action-header {
  text-align: right;
  padding-right: 1rem;
}

.ingredient-cell {
  overflow-wrap: anywhere;
  word-break: break-word;
}

.action-cell {
  white-space: nowrap;
  text-align: right;
  padding-right: 1rem;
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

.delete-inline-error {
  display: inline;
  margin-left: 0.25rem;
  color: #e53935;
  font-size: 0.86rem;
  line-height: 1.2;
}

.empty-list {
  border: 1px dashed rgba(244, 209, 74, 0.5);
  border-radius: 12px;
  padding: 1rem;
  color: #f4d14a;
}

@media (max-width: 768px) {
  .list-table {
    min-width: 920px;
  }

  .list-wrap {
    overflow-x: scroll;
    padding-bottom: 0.75rem;
    scrollbar-width: auto;
    scrollbar-color: #ffd84f #101216;
    scrollbar-gutter: stable both-edges;
  }

  .list-wrap::-webkit-scrollbar {
    height: 10px;
  }

  .list-wrap::-webkit-scrollbar-track {
    background: #101216;
  }

  .list-wrap::-webkit-scrollbar-thumb {
    background: #ffd84f;
    border-radius: 999px;
  }
}
</style>
