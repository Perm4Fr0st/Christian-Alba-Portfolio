<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import { router } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Skills',
        href: '/skills',
    },
];

const { skills } = defineProps<{
    skills: {
        data: Array<any>;
        current_page: number;
        per_page: number;
        links: Array<{ label: string; url: string | null; active: boolean }>;
    };
}>();

const showDeleteModal = ref(false);
const skillToDelete = ref<{ id: number; name: string } | null>(null);
const renderKey = ref(0);

function openDeleteModal(skill) {
    skillToDelete.value = skill;
    showDeleteModal.value = true;
}

function closeDeleteModal() {
    skillToDelete.value = null;
    showDeleteModal.value = false;
}
async function confirmDelete() {
    if (!skillToDelete.value) return;

    router.delete(route('skills.delete', skillToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Skill deleted!');
            renderKey.value++;
            skillToDelete.value = null;
            showDeleteModal.value = false;
        },
        onError: () => {
            toast.error('Something went wrong while deleting the skill.');
        }
    });
}
function goToPage(url: string | null) {
    if (url) {
        router.visit(url, {
            preserveScroll: true,
            preserveState: true,
        });
    }
}
</script>

<template>
    <Head title="Skills" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">

            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">All Skills</h1>
                <a 
                    href="/skills/create"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transform hover:scale-105 hover:shadow-lg transition cursor-pointer duration-200"
                >
                    Add
                </a>
            </div>


            <div class="overflow-x-auto rounded-xl border border-gray-300 dark:border-gray-600" :key="renderKey">
                <table class="table-auto w-full border-collapse bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                    <thead class="bg-gray-100 dark:bg-gray-800 border-b border-gray-300 dark:border-gray-600">
                    <tr>
                        <th class="px-4 py-2 text-left border-r border-gray-300 dark:border-gray-600">#</th>
                        <th class="px-4 py-2 text-left border-r border-gray-300 dark:border-gray-600">name</th>
                        <th class="px-4 py-2 text-left border-r border-gray-300 dark:border-gray-600">Image</th>
                        <th class="px-4 py-2 text-left border-gray-300 dark:border-gray-600 w-20">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                        <template v-if="skills?.data?.length > 0">
                            <tr v-for="(skill, index) in skills.data" :key="skill.id" class="border-t border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800">
                                <th class="px-4 py-2 text-left border-r border-gray-300 dark:border-gray-600">{{ (skills?.current_page - 1) * skills?.per_page + index + 1 }}</th>
                                <td class="px-4 py-2 border-r border-gray-300 dark:border-gray-600">{{ skill.name }}</td>
                                <td class="px-4 py-2 border-r border-gray-300 dark:border-gray-600">
                                    <img :src="`/storage/${skill.image}`" alt="Skill image" class="h-10 w-auto object-contain" />
                                </td>
                                <td class="px-4 py-2 border-r border-gray-300 dark:border-gray-600 flex space-x-2 cursor-pointer">
                                    <a
                                    :href="`/skills/${skill.id}/edit`"
                                    class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-3 py-1 rounded"
                                    >
                                    Edit
                                    </a>
                                    <a
                                     @click="openDeleteModal(skill)"
                                    class="bg-red-500 hover:bg-red-600 text-white text-sm px-3 py-1 rounded"
                                    >
                                    Delete
                                    </a>
                                </td>
                            </tr>
                        </template>
                        <template v-else>
                            <tr class="border-t border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800">
                                <td colspan="4" class="px-4 py-2 text-center text-gray-500 dark:text-gray-400">Skill table is empty </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex justify-center space-x-2">
                <button 
                    v-for="link in skills.links" 
                    :key="link.label" 
                    @click="goToPage(link.url)"
                    v-html="link.label"
                    :disabled="!link.url"
                    :class="[
                        'px-3 py-1 border rounded',
                        link.active ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-700',
                        !link.url 
                            ? 'opacity-50 cursor-not-allowed' 
                            : 'hover:bg-blue-100 dark:hover:bg-gray-600 cursor-pointer'
                    ]"
                ></button>
            </div>
        </div>
    </AppLayout>
    <div 
        v-if="showDeleteModal" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-transparent backdrop-blur-sm"
    >
        <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm rounded-lg p-6 w-full max-w-md shadow-lg">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                Delete Skill
            </h2>
            <p class="text-gray-700 dark:text-gray-300 mb-6">
                Are you sure you want to delete <strong>{{ skillToDelete?.name }}</strong>?
            </p>
            <div class="flex justify-end space-x-2">
                <button 
                    @click="closeDeleteModal"
                    class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-600 cursor-pointer"
                >
                    Cancel
                </button>
                <button 
                    @click="confirmDelete"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 cursor-pointer"
                >
                    Delete
                </button>
            </div>
        </div>
    </div>
</template>

