<script setup>
import { ref, computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Table from '@/Components/Table.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectPermissions from '@/Pages/Admin/Partials/SelectPermissions.vue';

const props = defineProps({
    roles: Object,
    params: Object,
    buffer: Boolean,
    middleware: Array
});

const permissionsForItem = ref(false);
const confirmingDeletion = ref(false);
const editing = ref(false);
const itemToDelete = ref(false);
const itemToEdit = ref(false);
let formPermissions = ref(false);
const formDelete = useForm({});
const formEdit = useForm({
    name: '',
});
const formFilter = useForm({
    term: '',
});

formFilter.term = props.params.term

// deleting

const closeDeleteModal = () => {
    confirmingDeletion.value = false;
    itemToDelete.value = null
};

const openDeleteModal = (id) => {
    confirmingDeletion.value = true;
    itemToDelete.value = id
};

const deleteItem = () => {
    formDelete.delete(route('role.delete', itemToDelete.value), {
        preserveScroll: true,
        onSuccess: () => closeDeleteModal(),
        onError: () => {},
    });
};

// editing

const closeEditModal = () => {
    editing.value = false;
    itemToEdit.value = null

    formEdit.reset()
};

const openEditModal = (id) => {
    editing.value = true;
    itemToEdit.value = id;

    const roleTemp = props.roles.data.filter(role => { return role.id === id })[0]
    permissionsForItem.value = roleTemp.middlewares.map(mw => mw.name)

    formEdit.name = roleTemp.name
};

const editItemData = () => {
    formEdit.transform(data => ({
        ...data,
        middlewares: formPermissions.value
    })).put(route('role.update', itemToEdit.value), {
        preserveScroll: true,
        onSuccess: () => closeEditModal(),
        onError: () => {},
    });
}

const formChange = (data) => {
    formPermissions.value = data
}
</script>

<template>
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="mt-5 md:mt-0 md:col-span-1 p-2">
            <InputLabel for="term" value="Iskalni niz" />
            <TextInput
                id="term"
                v-model="formFilter.term"
                type="text"
                class="mt-1 block w-full"
                autocomplete="term"
            />
        </div>
        <div class="mt-5 md:mt-0 md:col-span-2 p-2 text-right flex items-end justify-end">
            <Link preserve-scroll preserve-state :href="route('roles')" :data="{ page: 1, term: '' }" class="mr-1" @click="formFilter.reset()">
                <SecondaryButton>
                    Reset
                </SecondaryButton>
            </Link>

            <Link preserve-scroll preserve-state :href="route('roles')" :data="{ page: 1, term: formFilter.term }">
                <PrimaryButton>
                    Apply
                </PrimaryButton>
            </Link>
        </div>
    </div>
    
    <Table :data="roles" :headerNames="['Ime']" 
        :sortedAs="['name']" 
        :allowEdit="middleware.includes('roles.edit')" :allowDelete="middleware.includes('roles.delete')" 
        @edit="openEditModal" 
        @delete="openDeleteModal"
        :query="{ term: formFilter.term }"
        :buffer="buffer"
        routeName="roles"
    />

    <!-- deleting -->

    <DialogModal :show="confirmingDeletion" @close="closeDeleteModal"> 
        <template #title>
            Delete Account
        </template>

        <template #content>
            Are you sure you want to delete this account? Once this account is deleted, all of its resources and data will be permanently deleted.

            <InputError :message="formDelete.errors.delete" class="mt-2" />
        </template>

        <template #footer>
            <SecondaryButton @click="closeDeleteModal">
                Cancel
            </SecondaryButton>

            <DangerButton
                class="ml-3"
                @click="deleteItem"
            >
                Delete Account
            </DangerButton>
        </template>
    </DialogModal>

    <!-- editing -->

    <DialogModal :show="editing" @close="closeEditModal"> 
        <template #title>
            Edit Account
        </template>

        <template #content>
            <form @submit.prevent="">
                <div
                    class="px-4 py-5 bg-white sm:p-6"
                >
                    <!-- Name -->
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="name" value="Ime" />
                            <TextInput
                                id="name"
                                v-model="formEdit.name"
                                type="text"
                                class="mt-1 block w-full"
                                autocomplete="name"
                            />
                            <InputError :message="formEdit.errors.name" class="mt-2" />
                        </div>

                        <SelectPermissions :data="permissionsForItem" @formChange="formChange"/>
                    </div>
                </div>
            </form>
        </template>

        <template #footer>
            <SecondaryButton @click="closeEditModal">
                Cancel
            </SecondaryButton>

            <PrimaryButton
                class="ml-3"
                @click="editItemData"
            >
                Save
            </PrimaryButton>
        </template>
    </DialogModal>
</template>