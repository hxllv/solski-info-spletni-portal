<script setup>
import { useForm, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Table from '@/Components/Table.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import Select from '@/Components/Select.vue';
import Timetable from '@/Components/Timetable.vue';

const props = defineProps({
    sClass: Object,
    classTeacher: Object,
    potentialStudents: Object,
    roles: Array,
    subjects: Array,
    timetableEntries: Array,
    hours: Object,
    students: Object,
    params: Object,
    permission: Array,
});

const addingUsers = ref(false);
const selectedUsersAdding = ref(false);
const selectedUsers = ref(false);
const multiActionsAddingClass = ref('opacity-30 pointer-events-none');
const multiActionsClass = ref('opacity-30 pointer-events-none');

const formFilter = useForm({
    term: '',
    role: ''
});

formFilter.term = props.params.term
formFilter.role = props.params.role

const formFilterAdding = useForm({
    term_adding: '',
    role_adding: '',
});

formFilterAdding.term_adding = props.params.term_adding
formFilterAdding.role_adding = props.params.role_adding

const formDissociate = useForm({});
const formAssociate = useForm({});

// associating users

const onAddingSelectChange = (val) => {
    selectedUsersAdding.value = val.length > 0 ? val : false;

    multiActionsAddingClass.value = ''
    if (!selectedUsersAdding.value)
        multiActionsAddingClass.value = 'opacity-30 pointer-events-none'
}

const closeAddingModal = () => {
    addingUsers.value = false;
    formFilterAdding.reset();

    router.get(
        route('view.class', props.sClass.id), 
        {
            page: props.params.page, 
            term: props.params.term,
            role: props.params.role,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    )
};

const openAddingModal = () => {
    addingUsers.value = true;
};

const addUsers = () => {
    formAssociate.transform(() => ({
        userIds: selectedUsersAdding.value,
        class: props.sClass.id
    })).put(route('multi.user.update'), {
        preserveScroll: true,
        onSuccess: () => closeAddingModal(),
        onError: () => {},
    });
};

// dissociating users

const onSelectChange = (val) => {
    selectedUsers.value = val.length > 0 ? val : false;

    multiActionsClass.value = ''
    if (!selectedUsers.value)
        multiActionsClass.value = 'opacity-30 pointer-events-none'
}

const dissociateUsers = () => {
    formDissociate.transform(() => ({
        userIds: selectedUsers.value,
        class: -1
    })).put(route('multi.user.update'), {
        preserveScroll: true,
    });
};

const refresh = () => {
    router.get(
        route('view.class', props.sClass.id), 
        {
            page: props.params.page, 
            term: props.params.term,
            role: props.params.role,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    )
}

// modifying users

const hatedProps = ['created_at', 'current_team_id', 'email_verified_at', 'role_id', 'name', 'surname', 'two_factor_confirmed_at', 'updated_at'];

const usersMod = computed(() => {
    let newUsers = JSON.parse(JSON.stringify(props.students));

    newUsers.data.forEach(user => {
        user.fullname = `${user.name} ${user.surname}`
        if (!user.is_registered)
            user.fullname += ' (neregistriran)'
        user.role = user.role.name

        hatedProps.forEach(prop => {
            delete user[prop]
        })
    })

    return newUsers
});

const usersModAdding = computed(() => {
    let newUsers = JSON.parse(JSON.stringify(props.potentialStudents));

    newUsers.data.forEach(user => {
        user.fullname = `${user.name} ${user.surname}`
        if (!user.is_registered)
            user.fullname += ' (neregistriran)'
        user.role = user.role.name
        user.class = user.student_of ? user.student_of.name : '/'

        hatedProps.forEach(prop => {
            delete user[prop]
        })
    })

    return newUsers
});
</script>

<template>
    <AdminLayout :title="`Podrobnosti razreda ${sClass.name}`" :backButtonURL="route('classes')" v-slot="layout" :permission="permission">
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div>
                <h2 class="pb-6 px-2 text-xl text-gray-800 leading-tight">
                    Razrednik: {{ `${classTeacher.name} ${classTeacher.surname} - ${classTeacher.email}` }}
                </h2>

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
                    <div class="mt-5 md:mt-0 md:col-span-1 p-2">
                        <InputLabel for="roleFilter" value="Skupina pravic" />
                        <Select
                            id="roleFilter"
                            v-model="formFilter.role"
                            class="mt-1 block w-full"
                            autocomplete="role"
                        >
                            <option value="">Vsi</option>
                            <option v-for="role in roles" :value="role.id">{{role.name}}</option>
                        </Select>
                    </div>

                    <div class="mt-5 md:mt-0 md:col-span-1 p-2 text-right flex items-end justify-end">
                        <Link preserve-scroll preserve-state :href="route('view.class', sClass.id)" :data="{ page: 1, term: '', role: '' }" class="mr-1" @click="formFilter.reset()">
                            <SecondaryButton>
                                Ponastavi
                            </SecondaryButton>
                        </Link>

                        <Link preserve-scroll preserve-state :href="route('view.class', sClass.id)" :data="{ page: 1, term: formFilter.term, role: formFilter.role }">
                            <PrimaryButton>
                                Uporabi
                            </PrimaryButton>
                        </Link>
                    </div>
                </div>

                <!-- multi actions -->
                <div class="md:grid md:grid-cols-3 md:gap-6" v-if="permission.includes('classes.edit')">
                    <div class="mt-5 md:mt-0 md:col-span-1 p-2">
                        <SecondaryButton class="mr-1" :class="multiActionsClass" @click="dissociateUsers">
                            Odstrani iz razreda
                        </SecondaryButton>
                        <PrimaryButton @click="openAddingModal">
                            Dodaj v razred
                        </PrimaryButton>
                    </div>
                </div>

                <Table :data="usersMod" :headerNames="['Ime', 'Email', 'Skupina pravic']" 
                    :sortedAs="['fullname', 'email', 'role']" 
                    :allowEdit="false" :allowDelete="false" :allowMultiActions="permission.includes('classes.edit')"
                    :query="{ term: formFilter.term, role: formFilter.role }"
                    :buffer="layout.buffer"
                    @selectedChange="onSelectChange"
                    routeName="view.class"
                    :routeParams="[sClass.id]"
                />

                <div class="p-4"></div>

                <Timetable :subjects="subjects" :classId="sClass.id" :data="timetableEntries" :hours="hours" :allowEdit="permission.includes('timetable.edit')" :buffer="layout.buffer" @refresh="refresh" />

                <DialogModal :show="addingUsers" @close="closeAddingModal" maxWidth="4xl"> 
                    <template #title>
                        Dodaj uporabnike
                    </template>
                    <template #content>
                        <div class="md:grid md:grid-cols-3 md:gap-6">
                            <div class="mt-5 md:mt-0 md:col-span-1 p-2">
                                <InputLabel for="term" value="Iskalni niz" />
                                <TextInput
                                    id="term"
                                    v-model="formFilterAdding.term_adding"
                                    type="text"
                                    class="mt-1 block w-full"
                                    autocomplete="term"
                                />
                            </div>
                            <div class="mt-5 md:mt-0 md:col-span-1 p-2">
                                <InputLabel for="roleFilter" value="Skupina pravic" />
                                <Select
                                    id="roleFilter"
                                    v-model="formFilterAdding.role_adding"
                                    class="mt-1 block w-full"
                                    autocomplete="role"
                                    :defaultValue="''"
                                >
                                    <option value="">Vsi</option>
                                    <option v-for="role in roles" :value="role.id">{{role.name}}</option>
                                </Select>
                            </div>
                            <div class="mt-5 md:mt-0 md:col-span-1 p-2 text-right flex items-end justify-end">
                                <Link preserve-scroll preserve-state :href="route('view.class', sClass.id)" :data="{ page: props.params.page, ps_page: 1, term_adding: '', role_adding: '' }" class="mr-1" @click="formFilterAdding.reset()">
                                    <SecondaryButton>
                                        Ponastavi
                                    </SecondaryButton>
                                </Link>

                                <Link preserve-scroll preserve-state :href="route('view.class', sClass.id)" :data="{ page: props.params.page, ps_page: 1, term_adding: formFilterAdding.term_adding, role_adding: formFilterAdding.role_adding }">
                                    <PrimaryButton>
                                        Uporabi
                                    </PrimaryButton>
                                </Link>
                            </div>
                        </div>
                        <Table :data="usersModAdding" :headerNames="['Ime', 'Email', 'Skupina pravic', 'Razred']" 
                            :sortedAs="['fullname', 'email', 'role', 'class']" 
                            :allowEdit="false" :allowDelete="false" :allowMultiActions="true"
                            :query="{ page: props.params.page, term: formFilter.term, role: formFilter.role, term_adding: formFilterAdding.term_adding, role_adding: formFilterAdding.role_adding }"
                            :buffer="layout.buffer"
                            @selectedChange="onAddingSelectChange"
                            pageName="ps_page"
                            routeName="view.class"
                            :routeParams="[sClass.id]"
                        />
                    </template>

                    <template #footer>
                        <SecondaryButton @click="closeAddingModal">
                            Prekliči
                        </SecondaryButton>

                        <PrimaryButton
                            class="ml-3"
                            :class="multiActionsAddingClass"
                            @click="addUsers"
                        >
                            Shrani
                        </PrimaryButton>
                    </template>
                </DialogModal>
            </div>
        </div>
    </AdminLayout>
</template>