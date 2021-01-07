<template>
  <div class="d-flex align-items-center p-2">
    <div class="symbol symbol-50 mr-5">
      <span class="symbol-label">
        <img :src="item.type.icon" class="module-icon" :alt="item.type.name">
      </span>
    </div>
    <div class="d-flex flex-column flex-grow-1">
      <span class="font-size-h6 text-dark-75">{{ item.type.name }}</span>
    </div>
    <div v-show="isEditing" class="count-form mr-5">
      <div v-if="item.type.tech_level === 2" class=" form-group mb-0 mr-2">
        <div class="input-group input-group-sm">
          <div class="input-group-prepend">
            <span class="input-group-text">
              <i class="fas fa-microscope"></i>
            </span>
          </div>
          <input ref="inventionCountInput" v-model="inventionCount" type="number" class="form-control form-control-sm" placeholder="0" @keyup.enter="saveTrackedItem">
        </div>
      </div>
      <div class="form-group mb-0 mr-2">
        <div class="input-group input-group-sm">
          <div class="input-group-prepend">
            <span class="input-group-text">
              <i class="fas fa-industry"></i>
            </span>
          </div>
          <input ref="productionCountInput" v-model="productionCount" type="number" class="form-control form-control-sm" placeholder="0" @keyup.enter="saveTrackedItem">
        </div>
      </div>
    </div>
    <div v-show="!isEditing" class="count-form justify-content-center mr-5">
      <div v-if="item.type.tech_level === 2" class="form-group mb-0 mr-2">
        <div class="input-group input-group-sm">
          <div class="input-group-prepend">
            <span class="input-group-text">
              <i class="fas fa-microscope"></i>
            </span>
            <span class="input-group-text">{{ item.invention_count }}</span>
          </div>
          <input v-model="invented" type="number" class="form-control form-control-sm" placeholder="0" @keyup.enter="updateDoneCounts">
          <div class="input-group-append">
            <span class="input-group-text">{{ item.invention_count - item.invented }}</span>
          </div>
        </div>
      </div>
      <div class="form-group mb-0 mr-2">
        <div class="input-group input-group-sm">
          <div class="input-group-prepend">
            <span class="input-group-text">
              <i class="fas fa-industry"></i>
            </span>
            <span class="input-group-text">{{ item.production_count }}</span>
          </div>
          <input v-model="produced" type="number" class="form-control form-control-sm" placeholder="0" @keyup.enter="updateDoneCounts">
          <div class="input-group-append">
            <span class="input-group-text">{{ item.production_count - item.produced }}</span>
          </div>
        </div>
      </div>
    </div>
    <div class="btn btn-hover-light-primary btn-sm btn-icon" @click="toggleEditMode">
      <i class="fas fa-edit"></i>
    </div>
    <div class="btn btn-hover-light-danger btn-sm btn-icon" @click="$emit('delete')">
      <i class="fas fa-trash"></i>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TrackedModule',
  props: {
    item: {
      required: true,
      type: Object,
    },
  },
  data: () => ({
    isEditing: false,
    productionCount: 0,
    inventionCount: 0,
    produced: 0,
    invented: 0,
  }),
  watch: {
    item: {
      deep: true,
      immediate: true,
      handler(val) {
        this.productionCount = val.production_count;
        this.inventionCount = val.invention_count;
      },
    }
  },
  methods: {
    toggleEditMode() {
      this.isEditing = !this.isEditing;
      if (this.isEditing) {
        this.$nextTick(() => {
          this.item.type.tech_level === 2 ? this.$refs.inventionCountInput.focus() : this.$refs.productionCountInput.focus();
        })
      }
    },
    async saveTrackedItem() {
      if (this.productionCount < 1) {
        return;
      }

      await this.$api.editTrackedItem(this.item.id, this.productionCount, this.inventionCount);
      this.item.production_count = this.productionCount;
      this.item.invention_count = this.inventionCount;

      this.toggleEditMode();
    },
    async updateDoneCounts() {
      const produced = Number(this.produced);
      const invented = Number(this.invented);
      if (produced === 0 && invented === 0) {
        return;
      }

      await this.$api.updateTrackedItemDoneCounts(this.item.id, produced, invented);

      this.item.produced += produced;
      this.item.invented += invented;

      this.produced = 0;
      this.invented = 0;
    },
  },
}
</script>

<style lang="scss" scoped>
.module-icon {
  width: 44px;
  height: 44px;

  border-radius: 0.42rem;
}

.count-form {
  display: flex;

  input {
    max-width: 60px;
  }
}
</style>
