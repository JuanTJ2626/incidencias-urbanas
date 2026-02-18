<template>
  <div>
    <h2>REPORTES Incidencias</h2>

    <DataTable
      :value="customers"
      paginator
      :rows="5"
      :rowsPerPageOptions="[5, 10, 20, 50]"
      tableStyle="min-width: 50rem"
    >
      <Column header="Name" style="width: 10%">
        <template #body="slotProps">
          <strong>{{ slotProps.data.name }}</strong>
        </template>
      </Column>

      <Column header="Country" style="width: 10%">
        <template #body="slotProps">
          {{ slotProps.data.country.toUpperCase() }}
        </template>
      </Column>

      <Column field="company" header="Company" style="width: 10%"></Column>
      <Column field="representative" header="Representative" style="width: 10%"></Column>
    </DataTable>
  </div>
</template>

<script>
import DataTable from "primevue/datatable";
import Column from "primevue/column";

export default {
  components: {
    DataTable,
    Column,
  },
  data() {
    return {
      customers: [
        {
          id: 1,
          name: "Juan Pérez",
          country: "Mexico",
          company: "Empresa A",
          representative: "Maria",
        },
        {
          id: 2,
          name: "Ana Gómez",
          country: "Colombia",
          company: "Empresa B",
          representative: "Carlos",
        },
        {
          id: 3,
          name: "Luis Martínez",
          country: "Argentina",
          company: "Empresa C",
          representative: "Lucía",
        },
        {
          id: 4,
          name: "Sofía Díaz",
          country: "Chile",
          company: "Empresa D",
          representative: "Pedro",
        },
        {
          id: 5,
          name: "Carlos Ruiz",
          country: "Peru",
          company: "Empresa E",
          representative: "Elena",
        },
      ],
    };
  },
};
</script>
