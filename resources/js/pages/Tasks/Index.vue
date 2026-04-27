<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Tarefas',
                href: '/tasks',
            },
        ],
    },
});

type Task = {
    id: number;
    title: string;
    description: string;
    status: 'pending' | 'in progress' | 'completed';
    created_at_human: string;
};

const tasks = ref<Task[]>([]);
const currentFilter = ref('');
const isLoading = ref(true);
const isSubmitting = ref(false);

const newTask = ref({
    title: '',
    description: '',
});

const fetchTasks = async (status = '') => {
    isLoading.value = true;
    currentFilter.value = status;

    try {
        const query = status ? `?status=${status}` : '';
        const res = await fetch(`/api/tasks${query}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'include',
        });
        const json = await res.json();
        tasks.value = json.data || [];
    } catch (e) {
        console.error('Failed to load tasks', e);
    } finally {
        isLoading.value = false;
    }
};

const createTask = async () => {
    if (!newTask.value.title) return;
    
    isSubmitting.value = true;
    try {
        const res = await fetch(`/api/tasks`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            credentials: 'include',
            body: JSON.stringify({ 
                title: newTask.value.title, 
                description: newTask.value.description, 
                status: 'pending' 
            })
        });
        if (res.ok) {
            newTask.value = { title: '', description: '' };
            fetchTasks(currentFilter.value);
        }
    } catch (e) {
        console.error(e);
    } finally {
        isSubmitting.value = false;
    }
};

const markAsCompleted = async (task: Task) => {
    try {
        const res = await fetch(`/api/tasks/${task.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            credentials: 'include',
            body: JSON.stringify({ title: task.title, description: task.description, status: 'completed' })
        });
        if (res.ok) fetchTasks(currentFilter.value);
    } catch (e) {
        console.error(e);
    }
};

const deleteTask = async (id: number) => {
    if (!confirm('Deseja realmente excluir esta tarefa?')) return;
    try {
        const res = await fetch(`/api/tasks/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            credentials: 'include',
        });
        if (res.ok) fetchTasks(currentFilter.value);
    } catch (e) {
        console.error(e);
    }
};

onMounted(() => {
    fetchTasks();
});

const getStatusColor = (status: string) => {
    switch (status) {
        case 'pending': return 'bg-amber-500/15 text-amber-700 dark:text-amber-400 hover:bg-amber-500/25 border-0';
        case 'in progress': return 'bg-blue-500/15 text-blue-700 dark:text-blue-400 hover:bg-blue-500/25 border-0';
        case 'completed': return 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-500/25 border-0';
        default: return '';
    }
};

const getStatusLabel = (status: string) => {
    switch (status) {
        case 'pending': return 'Pendente';
        case 'in progress': return 'Em Progresso';
        case 'completed': return 'Concluída';
        default: return status;
    }
};
</script>

<template>
    <Head title="Gerenciar Tarefas" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6 mx-auto max-w-7xl w-full">
        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 w-full">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-foreground">Minhas Tarefas</h1>
                <p class="text-muted-foreground mt-1 text-sm">Gerencie, filtre e crie novas tarefas na sua API Restful.</p>
            </div>

            <div class="flex gap-2 p-1 bg-muted rounded-lg shrink-0">
                <Button
                    :variant="currentFilter === '' ? 'default' : 'ghost'"
                    size="sm"
                    class="rounded-md transition-all"
                    @click="fetchTasks('')"
                >
                    Todas
                </Button>
                <Button
                    :variant="currentFilter === 'pending' ? 'default' : 'ghost'"
                    size="sm"
                    class="rounded-md transition-all"
                    @click="fetchTasks('pending')"
                >
                    Pendentes
                </Button>
                <Button
                    :variant="currentFilter === 'completed' ? 'default' : 'ghost'"
                    size="sm"
                    class="rounded-md transition-all"
                    @click="fetchTasks('completed')"
                >
                    Concluídas
                </Button>
            </div>
        </div>

        <!-- Create Task Form -->
        <Card class="mb-4">
            <CardHeader class="pb-3 text-sm">
                <CardTitle class="text-base">Adicionar Nova Tarefa</CardTitle>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="createTask" class="flex flex-col sm:flex-row gap-4 items-start">
                    <div class="flex-1 w-full">
                        <input 
                            v-model="newTask.title" 
                            type="text" 
                            placeholder="Título da tarefa..." 
                            required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        />
                    </div>
                    <div class="flex-1 w-full">
                        <input 
                            v-model="newTask.description" 
                            type="text" 
                            placeholder="Descrição curta (opcional)" 
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        />
                    </div>
                    <Button type="submit" size="sm" class="h-10 shrink-0 w-full sm:w-auto" :disabled="isSubmitting || !newTask.title">
                        <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Criar Tarefa
                    </Button>
                </form>
            </CardContent>
        </Card>

        <!-- Loading State -->
        <div v-if="isLoading" class="flex justify-center items-center py-20">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        </div>

        <!-- Empty State -->
        <div v-else-if="tasks.length === 0" class="flex flex-col items-center justify-center p-12 text-center border border-dashed rounded-xl border-border bg-card/50">
            <h3 class="mt-4 text-lg font-semibold">Nenhuma tarefa encontrada</h3>
            <p class="mb-4 mt-2 text-sm text-muted-foreground">Você ainda não tem tarefas com este status.</p>
        </div>

        <!-- Task Grid -->
        <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <Card v-for="task in tasks" :key="task.id" class="group relative overflow-hidden transition-all hover:shadow-md border-border/60 hover:border-border">
                <div class="absolute left-0 top-0 w-1 h-full"
                    :class="{
                        'bg-amber-500': task.status === 'pending',
                        'bg-blue-500': task.status === 'in progress',
                        'bg-emerald-500': task.status === 'completed'
                    }">
                </div>

                <CardHeader class="pb-3 pl-6">
                    <div class="flex justify-between items-start gap-4">
                        <CardTitle class="text-xl leading-tight line-clamp-2">{{ task.title }}</CardTitle>
                        <Badge :class="getStatusColor(task.status)">
                            {{ getStatusLabel(task.status) }}
                        </Badge>
                    </div>
                </CardHeader>

                <CardContent class="pl-6">
                    <CardDescription class="line-clamp-3 text-sm min-h-[60px]">
                        {{ task.description || 'Sem descrição detalhada.' }}
                    </CardDescription>
                </CardContent>

                <CardFooter class="p-6 flex items-center justify-between border-t border-border/40 pt-4 bg-muted/20">
                    <div class="text-xs text-muted-foreground font-medium">#{{ task.id }}</div>

                    <div class="flex gap-2">
                        <Button
                            v-if="task.status !== 'completed'"
                            variant="secondary"
                            size="sm"
                            class="h-8 text-xs shrink-0"
                            @click="markAsCompleted(task)"
                        >
                            Concluir
                        </Button>
                        <Button
                            variant="destructive"
                            size="sm"
                            class="h-8 w-8 p-0 shrink-0 transition-opacity"
                            @click="deleteTask(task.id)"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                        </Button>
                    </div>
                </CardFooter>
            </Card>
        </div>
    </div>
</template>
