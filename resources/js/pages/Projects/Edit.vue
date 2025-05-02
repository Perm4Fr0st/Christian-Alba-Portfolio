<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import { router } from '@inertiajs/vue3';
import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.css';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Projects',
        href: '/projects',
    },
];

const props = defineProps<{
    projects: {
        id: number;
        name: string;
        link: link;
        image: string | null;
        skills: { id: number; name: string }[];
    },
    skills: { id: number; name: string }[];
}>();

const form = useForm({
    name: props.projects.name || '',
    link: props.projects.link || '',
});

const skillOptions = props.skills.map(skill => ({
    label: skill.name,
    value: skill.id
}));

const selectedSkills = ref<{ label: string; value: number }[]>(
    props.projects.skills.map(skill => ({
        label: skill.name,
        value: skill.id
    }))
);
const renderKey = ref(0);
const file = ref<File | null>(null);
const fileError = ref<string | null>(null);
const imagePreview = ref<string | null>(null);

onMounted(() => {
    if (props.projects.image) {
        imagePreview.value = `/storage/${props.projects.image}`;
    }
});

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const selectedFile = target.files?.[0];

    fileError.value = null;

    if (selectedFile) {
        const isImage = selectedFile.type.startsWith('image/');
        const isSizeValid = selectedFile.size <= 30 * 1024 * 1024;

        if (!isImage) {
            fileError.value = 'Only image files are allowed.';
            file.value = null;
        } else if (!isSizeValid) {
            fileError.value = 'File size must be less than or equal to 30MB.';
            file.value = null;
        } else {
            file.value = selectedFile;
            imagePreview.value = URL.createObjectURL(selectedFile);
        }
    }
};

async function submit() {
    if (!form.name.trim()) {
        toast.error('Name is required');
        return;
    }
    if (!form.link.trim()) {
        toast.error('Link is required');
        return;
    }
    const payload = new FormData();
    payload.append('name', form.name);
    payload.append('link', form.link);
    payload.append('_method', 'PUT');
    selectedSkills.value.forEach((skill, index) => {
        payload.append(`skills[${index}]`, skill.value.toString());
    });
    if (file.value) {
        payload.append('fileUpload', file.value);
    }

    router.post(route('projects.update', props.projects.id), payload, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Skill updated!');
            form.reset();
            selectedSkills.value = [];
            file.value = null;
            renderKey.value++;
        },
        onError: (errors) => {
            toast.error('Validation failed');
            console.error(errors);
        }
    });
}
</script>


<template>
    <Head title="Projects Update" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Update Skills</h1>
                <a 
                    href="/projects"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transform hover:scale-105 hover:shadow-lg transition cursor-pointer duration-200"
                >
                    Back
                </a>
            </div>

            <div class="flex justify-center items-center flex-1" :key="renderKey">
                <form @submit.prevent="submit" enctype="multipart/form-data" class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg w-full max-w-md space-y-4">
                    <div class="form-group">
                        <input 
                            type="text" 
                            class="form-control w-full p-2 rounded-md border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" 
                            id="name" 
                            placeholder="Enter Name"
                           v-model="form.name" 
                        >
                    </div>
                    <div class="form-group">
                        <input 
                            type="text" 
                            class="form-control w-full p-2 rounded-md border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" 
                            id="link" 
                            placeholder="Enter Link"
                           v-model="form.link" 
                        >
                    </div>
                    <div class="form-group">
                        <Multiselect
                            v-model="selectedSkills"
                            :options="skillOptions"
                            :multiple="true"
                            :close-on-select="false"
                            placeholder="Select skills"
                            label="label"
                            track-by="value"
                            class="dark:text-white"
                        />
                    </div>
                    <div v-if="imagePreview" class="mt-4">
                        <p class="text-gray-700 dark:text-gray-300 mb-2">Image Preview:</p>
                        <img 
                            :src="imagePreview" 
                            alt="Preview" 
                            class="max-h-64 rounded-md border border-gray-300 dark:border-gray-600 shadow"
                        >
                    </div>
                    <div class="form-group">
                        <label for="fileUpload" class="text-gray-700 dark:text-gray-300">
                            Upload an image (max 30MB)
                        </label>
                        <input 
                            type="file"
                            id="fileUpload"
                            accept="image/*"
                            @change="handleFileChange"
                            class="form-control-file w-full p-2 rounded-md border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
                        >
                        <p v-if="fileError" class="text-sm text-red-600 mt-2">{{ fileError }}</p>
                    </div>

                    <button 
                        type="submit" 
                        class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transform hover:scale-105 hover:shadow-lg cursor-pointer transition duration-200"
                        :disabled="!!fileError"
                    >
                        Update
                    </button>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
