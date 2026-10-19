<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { PhArrowsLeftRight } from '@phosphor-icons/vue';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

const page = usePage();
const { t, locale } = useI18n();
const open = ref(false);
const processing = ref(false);
const selected = ref(page.props.auth.workspaceMode);

function changeOpen(value: boolean) {
    if (processing.value) return;
    open.value = value;
    if (value) selected.value = page.props.auth.workspaceMode;
}

function submit() {
    if (
        processing.value ||
        !page.props.auth.workspaceOptions.includes(selected.value)
    )
        return;
    if (selected.value === page.props.auth.workspaceMode) {
        open.value = false;
        return;
    }
    processing.value = true;
    router.post(
        '/workspace-mode',
        { mode: selected.value },
        {
            preserveScroll: false,
            onSuccess: () => {
                open.value = false;
            },
            onFinish: () => {
                processing.value = false;
            },
        },
    );
}
</script>

<template>
    <Dialog :open="open" @update:open="changeOpen">
        <DialogTrigger as-child>
            <Button
                variant="outline"
                size="sm"
                class="w-full justify-start gap-2"
            >
                <PhArrowsLeftRight class="size-4" aria-hidden="true" />
                {{ t('workspace.switch') }}
            </Button>
        </DialogTrigger>
        <DialogContent
            class="workspace-shell max-h-[90dvh] overflow-y-auto text-start"
            :dir="locale === 'ar' ? 'rtl' : 'ltr'"
        >
            <DialogHeader class="text-start sm:text-start">
                <DialogTitle>{{ t('workspace.switch') }}</DialogTitle>
                <DialogDescription>{{
                    t('workspace.explanation')
                }}</DialogDescription>
            </DialogHeader>
            <form class="grid gap-5" @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="role-view-mode">{{
                        t('workspace.mode')
                    }}</Label>
                    <Select v-model="selected">
                        <SelectTrigger id="role-view-mode" class="w-full">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent class="max-h-[55dvh]">
                            <SelectItem
                                v-for="mode in page.props.auth.workspaceOptions"
                                :key="mode"
                                :value="mode"
                                >{{ t('workspace.modes.' + mode) }}</SelectItem
                            >
                        </SelectContent>
                    </Select>
                </div>
                <p class="text-xs text-muted-foreground">
                    {{ t('workspace.identity') }}
                </p>
                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        @click="changeOpen(false)"
                    >
                        {{ t('workspace.cancel') }}
                    </Button>
                    <Button type="submit" :disabled="processing">{{
                        t('workspace.apply')
                    }}</Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
