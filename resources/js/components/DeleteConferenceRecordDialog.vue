<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { PhCircleNotch, PhTrash } from '@phosphor-icons/vue';
import { ref, useId } from 'vue';
import { useI18n } from 'vue-i18n';
import InputError from '@/components/InputError.vue';
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
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const props = defineProps<{
    kind: 'series' | 'edition';
    code: string;
    identity: string;
    url: string;
}>();
const { t, te, locale } = useI18n();
const open = ref(false);
const id = useId();
const form = useForm({ confirmation_code: '' });
const errorMessage = (message: string | undefined) => {
    if (!message) return undefined;
    const key = `admin.deletion.errors.${message}`;
    return te(key) ? t(key) : t('admin.deletion.errors.failed');
};
const changeOpen = (value: boolean) => {
    if (form.processing) return;
    open.value = value;
    form.reset();
    form.clearErrors();
};
const submit = () => {
    if (form.processing || form.confirmation_code !== props.code) return;
    form.delete(props.url, {
        preserveScroll: true,
        onSuccess: () => {
            open.value = false;
            form.reset();
        },
        onError: (errors) => {
            form.setError(
                'confirmation_code',
                errors.confirmation_code ?? errors.delete ?? 'failed',
            );
        },
    });
};
</script>

<template>
    <Dialog :open="open" @update:open="changeOpen">
        <DialogTrigger as-child>
            <Button
                type="button"
                variant="destructive"
                size="sm"
                :aria-label="t('admin.deletion.deleteIdentity', { code })"
            >
                <PhTrash class="size-3.5" aria-hidden="true" />
                {{ t('admin.deletion.delete') }}
            </Button>
        </DialogTrigger>
        <DialogContent
            class="workspace-shell max-h-[90dvh] overflow-y-auto text-start"
            :dir="locale === 'ar' ? 'rtl' : 'ltr'"
            :show-close-button="false"
        >
            <form
                class="space-y-5"
                :aria-busy="form.processing"
                @submit.prevent="submit"
            >
                <DialogHeader class="text-start sm:text-start">
                    <DialogTitle>{{
                        t(`admin.deletion.${kind}Title`)
                    }}</DialogTitle>
                    <DialogDescription class="leading-6">{{
                        t(`admin.deletion.${kind}Description`)
                    }}</DialogDescription>
                </DialogHeader>
                <div class="border border-border bg-muted/50 px-4 py-3">
                    <p class="text-sm font-semibold">
                        <bdi>{{ code }}</bdi>
                    </p>
                    <p class="mt-1 text-sm break-words text-muted-foreground">
                        <bdi>{{ identity }}</bdi>
                    </p>
                </div>
                <div class="grid gap-2">
                    <Label :for="`${id}-code`">{{
                        t('admin.deletion.confirmLabel')
                    }}</Label>
                    <p :id="`${id}-help`" class="text-sm text-muted-foreground">
                        {{ t('admin.deletion.confirmHelp', { code }) }}
                    </p>
                    <Input
                        :id="`${id}-code`"
                        v-model="form.confirmation_code"
                        name="confirmation_code"
                        dir="ltr"
                        autocomplete="off"
                        autocapitalize="off"
                        spellcheck="false"
                        maxlength="255"
                        required
                        :disabled="form.processing"
                        :aria-invalid="!!form.errors.confirmation_code"
                        :aria-describedby="`${id}-help ${id}-error`"
                    />
                    <InputError
                        :id="`${id}-error`"
                        role="alert"
                        :message="errorMessage(form.errors.confirmation_code)"
                    />
                </div>
                <DialogFooter class="gap-2">
                    <Button
                        type="button"
                        variant="outline"
                        :disabled="form.processing"
                        @click="changeOpen(false)"
                        >{{ t('admin.deletion.cancel') }}</Button
                    >
                    <Button
                        type="submit"
                        variant="destructive"
                        :disabled="
                            form.processing || form.confirmation_code !== code
                        "
                    >
                        <PhCircleNotch
                            v-if="form.processing"
                            class="size-4 animate-spin"
                            aria-hidden="true"
                        />
                        {{
                            form.processing
                                ? t('admin.deletion.deleting')
                                : t('admin.deletion.confirm')
                        }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
