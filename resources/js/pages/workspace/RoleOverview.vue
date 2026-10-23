<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

defineProps<{ selectedMode: string }>();
const { t } = useI18n();
</script>

<template>
    <Head :title="t('workspace.title')" />
    <div
        class="flex w-full min-w-0 flex-col gap-6 p-4 [overflow-wrap:anywhere] md:p-8"
    >
        <header>
            <h1 class="text-2xl font-semibold tracking-tight">
                {{ t('workspace.title') }}
            </h1>
            <p class="mt-2 text-sm text-muted-foreground">
                {{ t('workspace.explanation') }}
            </p>
        </header>
        <Card>
            <CardHeader
                ><CardTitle>{{
                    t('workspace.modes.' + selectedMode)
                }}</CardTitle></CardHeader
            >
            <CardContent class="space-y-2 text-sm text-muted-foreground">
                <p>{{ t('workspace.notBuilt') }}</p>
                <p>{{ t('workspace.identity') }}</p>
            </CardContent>
        </Card>
    </div>
</template>
