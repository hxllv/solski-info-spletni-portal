<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Table from '@/Components/Table.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Select from '@/Components/Select.vue';

const props = defineProps({
    users: Array,
    roles: Array,
});

const confirmingUserDeletion = ref(false);
const userEditing = ref(false);
const userToDelete = ref(false);
const userToEdit = ref(false);
const formDelete = useForm({});
const formEdit = useForm({
    name: '',
    surname: '',
    email: '',
    role: ''
});

// deleting

const closeDeleteModal = () => {
    confirmingUserDeletion.value = false;
    userToDelete.value = null
};

const openDeleteModal = (id) => {
    confirmingUserDeletion.value = true;
    userToDelete.value = id
};

const deleteUser = () => {
    formDelete.delete(route('user.delete', userToDelete.value), {
        preserveScroll: true,
        onSuccess: () => closeDeleteModal(),
        onError: () => {},
    });
};

// editing

const closeEditModal = () => {
    userEditing.value = false;
    userToEdit.value = null

    formEdit.reset()
};

const openEditModal = (id) => {
    userEditing.value = true;
    userToEdit.value = id;

    const userTemp = props.users.filter(user => { return user.id === id })[0]

    formEdit.name = userTemp.name
    formEdit.surname = userTemp.surname
    formEdit.email = userTemp.email
    formEdit.role = String(userTemp.role_id)
};

const editUserData = () => {
    formEdit.put(route('user.update', userToEdit.value), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => closeEditModal(),
        onError: () => {},
    });
}

// preuredi podatke v users array objectih

const hatedProps = ['created_at', 'current_team_id', 'email_verified_at', 'role_id', 'name', 'surname', 'two_factor_confirmed_at', 'updated_at'];

const usersMod = computed(() => {
    let newUsers = JSON.parse(JSON.stringify(props.users));

    newUsers.forEach(user => {
        user.fullname = `${user.name} ${user.surname}`
        user.role = props.roles.filter(role => { return role.id === user.role_id })[0].name
        user.class = 'R1a'

        hatedProps.forEach(prop => {
            delete user[prop]
        })
    })

    return newUsers
});
</script>

<template>
    <div class="shadow">
        <Table :data="usersMod" :headerNames="['Ime', 'Email', 'Razred', 'Skupina pravic']" 
            :sortedAs="['fullname', 'email', 'class', 'role']" 
            :allowEdit="true" :allowDelete="true" 
            @edit="openEditModal" 
            @delete="openDeleteModal"
        />

        <!-- deleting -->

        <DialogModal :show="confirmingUserDeletion" @close="closeDeleteModal"> 
            <template #title>
                Delete Account
            </template>

            <template #content>
                Are you sure you want to delete this account? Once this account is deleted, all of its resources and data will be permanently deleted.
            </template>

            <template #footer>
                <SecondaryButton @click="closeDeleteModal">
                    Cancel
                </SecondaryButton>

                <DangerButton
                    class="ml-3"
                    @click="deleteUser"
                >
                    Delete Account
                </DangerButton>
            </template>
        </DialogModal>

        <!-- editing -->

        <DialogModal :show="userEditing" @close="closeEditModal"> 
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

                            <!-- Surname -->
                            <div class="col-span-6 sm:col-span-4">
                                <InputLabel for="surname" value="Priimek" />
                                <TextInput
                                    id="surname"
                                    v-model="formEdit.surname"
                                    type="text"
                                    class="mt-1 block w-full"
                                    autocomplete="surname"
                                />
                                <InputError :message="formEdit.errors.surname" class="mt-2" />
                            </div>

                            <!-- Email -->
                            <div class="col-span-6 sm:col-span-4">
                                <InputLabel for="email" value="Email" />
                                <TextInput
                                    id="email"
                                    v-model="formEdit.email"
                                    type="text"
                                    class="mt-1 block w-full"
                                    autocomplete="email"
                                />
                                <InputError :message="formEdit.errors.email" class="mt-2" />
                            </div>

                            <!-- Role -->
                            <div class="col-span-6 sm:col-span-4">
                                <InputLabel for="role" value="Skupina pravic" />
                                <Select
                                    id="role"
                                    v-model="formEdit.role"
                                    class="mt-1 block w-full"
                                    autocomplete="role"
                                >
                                    <option v-for="role in roles" :value="role.id">{{role.name}}</option>
                                </Select>
                                <InputError :message="formEdit.errors.role" class="mt-2" />
                            </div>
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
                    @click="editUserData"
                >
                    Save
                </PrimaryButton>
            </template>
        </DialogModal>
    </div>
</template>