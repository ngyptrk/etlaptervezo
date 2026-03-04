<template>
  <div
    class="table-responsive my-table-container"
    style="max-height: calc(100vh - 360px); overflow-y: auto"
  >
    <table class="table table-hover w-auto mx-auto my-themed-table">
      <thead class="sticky-top" style="z-index: 10; top: 0">
        <tr class="align-middle text-center">
          <th>Műveletek</th>
          <template v-for="col in columns" :key="col.key">
            <th
              v-if="col.debug >= 1"
              class="my-pointer"
              @click="$emit('sort', col.key)"
              :class="{ 'my-debug': col.debug == 1 }"
            >
              <div
                class="d-flex align-items-center justify-content-center text-nowrap"
              >
                <span>{{ col.label }}</span>
                <span
                  :class="{ invisible: sortColumn !== col.key }"
                  class="ms-1"
                >
                  {{ sortDirection === "asc" ? "▲" : "▼" }}
                </span>
              </div>
            </th>
          </template>
        </tr>
      </thead>
      <tbody class="table-group-divider">
        <tr
          v-for="item in items"
          :key="item.id"
          @click="onClickRow(item.id)"
          :class="{ 'table-primary': selectedId === item.id }"
        >
          <td>
            <ButtonsCrud
              :id="item.id"
              @delete="$emit('delete', $event)"
              @update="$emit('update', $event)"
              @create="$emit('create', $event)"
              @passwordChange="$emit('passwordChange', $event)"
              :cButtonVisible="cButtonVisible"
              :uButtonVisible="uButtonVisible"
              :dButtonVisible="dButtonVisible"
              :pButtonVisible="pButtonVisible"
            />
          </td>
          <template v-for="col in columns" :key="`${item.id}-${col.key}`">
            <td
              v-if="col.debug >= 1"
              :class="{ 'my-debug': col.debug == 1 }"
            >
              {{ item[col.key] }}
            </td>
          </template>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import ButtonsCrud from "./ButtonsCrud.vue";

export default {
  name: "GenericTable",
  props: {
    items: { type: Array, required: true },
    columns: { type: Array, required: true },
    useCollectionStore: { type: Function, required: true },
    cButtonVisible: { type: Boolean, default: true },
    uButtonVisible: { type: Boolean, default: true },
    dButtonVisible: { type: Boolean, default: true },
    pButtonVisible: { type: Boolean, default: false },
  },
  components: {
    ButtonsCrud,
  },
  data() {
    return {
      selectedId: null,
      store: null,
    };
  },
  created() {
    if (this.useCollectionStore) {
      this.store = this.useCollectionStore();
    }
  },
  computed: {
    sortColumn() {
      return this.store ? this.store.sortColumn : "";
    },
    sortDirection() {
      return this.store ? this.store.sortDirection : "asc";
    },
  },
  methods: {
    onClickRow(id) {
      this.selectedId = id;
    },
  },
};
</script>

<style scoped>
.my-table-container {
  border: 1px solid var(--color-border);
  border-radius: 8px;
  background: rgba(15, 15, 15, 0.8);
}

.my-themed-table {
  margin-left: auto;
  margin-right: auto;
  width: auto !important;
  color: #121315;
}

.my-themed-table thead th {
  background: linear-gradient(180deg, #1f2328, #17191d);
  color: #ffd84f;
  border-bottom: 2px solid #f4d14a;
}

.my-themed-table tbody td {
  background: #d5d5d6;
  border-color: #b4b6ba;
}

.my-themed-table tbody tr:nth-child(even) td {
  background: #cacacc;
}

.my-themed-table tbody tr:hover td {
  background: #f1de8d;
}

.my-themed-table tbody tr.table-primary td {
  background: #f4d14a !important;
  color: #151515;
  font-weight: 600;
}
</style>
