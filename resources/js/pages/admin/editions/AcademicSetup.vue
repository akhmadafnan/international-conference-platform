<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { PhArrowLeft, PhPlus } from '@phosphor-icons/vue';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

type WindowRow = {
    code: string;
    opens_at: string | null;
    closes_at: string | null;
    active: boolean;
};
const props = defineProps<{
    edition: { id: string; code: string; timezone: string };
    tracks: {
        id: string;
        code: string;
        name_i18n: Record<string, string>;
        active: boolean;
    }[];
    windows: WindowRow[];
    allowedCodes: string[];
    saved: boolean;
}>();
const { t, locale } = useI18n();
const languages = ['id', 'en', 'ar'] as const;
const url = computed(() => '/admin/editions/' + props.edition.id);
const trackForm = useForm({
    code: '',
    name_i18n: { id: '', en: '', ar: '' },
    active: true,
});
const windowForm = useForm({
    window_code: 'ABSTRACT_SUBMISSION',
    opens_at: '',
    closes_at: '',
    active: true,
});
const trackSubmit = () =>
    trackForm.post(url.value + '/tracks', {
        preserveScroll: true,
        onSuccess: () => trackForm.reset(),
    });
const windowSubmit = () =>
    windowForm.post(url.value + '/windows', { preserveScroll: true });
const windowLabel = (code: string) => t('admin.academic.windows.' + code);
</script>

<template>
    <Head :title="t('admin.academic.title')" />
    <div class="flex w-full min-w-0 flex-col gap-6 p-4 md:p-8">
        <header>
            <Link
                href="/admin/editions"
                class="admin-text-link text-sm text-muted-foreground"
            >
                <PhArrowLeft
                    class="size-4 rtl:-scale-x-100"
                    aria-hidden="true"
                />
                {{ t('admin.academic.back') }}
            </Link>
            <h1 class="mt-3 text-2xl font-semibold tracking-tight">
                {{ t('admin.academic.title') }} · <bdi>{{ edition.code }}</bdi>
            </h1>
            <p class="mt-2 text-sm text-muted-foreground">
                {{ t('admin.academic.description') }}
            </p>
        </header>
        <p
            v-if="saved"
            role="status"
            class="border border-primary/25 bg-primary/5 p-3 text-sm"
        >
            {{ t('admin.academic.saved') }}
        </p>
        <div class="grid min-w-0 gap-6 xl:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('admin.academic.tracks') }}</CardTitle>
                    <p class="text-sm text-muted-foreground">
                        {{ t('admin.academic.tracksHelp') }}
                    </p>
                </CardHeader>
                <CardContent class="space-y-5">
                    <p
                        v-if="!tracks.length"
                        class="text-sm text-muted-foreground"
                    >
                        {{ t('admin.academic.noTracks') }}
                    </p>
                    <div
                        v-for="track in tracks"
                        :key="track.id"
                        class="flex items-center justify-between gap-3 border-b border-border py-3"
                    >
                        <div>
                            <p class="text-sm font-medium">
                                <bdi>{{ track.code }}</bdi>
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{
                                    track.name_i18n[locale] ??
                                    track.name_i18n.en ??
                                    track.name_i18n.id
                                }}
                            </p>
                        </div>
                        <Badge variant="secondary">{{
                            track.active
                                ? t('admin.academic.active')
                                : t('admin.academic.inactive')
                        }}</Badge>
                    </div>
                    <form
                        class="grid gap-3 border-t border-border pt-4"
                        @submit.prevent="trackSubmit"
                    >
                        <h2 class="text-sm font-semibold">
                            {{ t('admin.academic.addTrack') }}
                        </h2>
                        <p
                            v-if="trackForm.hasErrors"
                            role="alert"
                            class="text-sm text-destructive"
                        >
                            {{ t('admin.academic.validationError') }}
                        </p>
                        <div class="grid gap-1.5">
                            <Label for="track-code">{{
                                t('admin.academic.code')
                            }}</Label>
                            <Input
                                id="track-code"
                                v-model="trackForm.code"
                                required
                                maxlength="255"
                                :aria-invalid="!!trackForm.errors.code"
                            />
                            <p
                                v-if="trackForm.errors.code"
                                class="text-xs text-destructive"
                            >
                                {{ trackForm.errors.code }}
                            </p>
                        </div>
                        <div
                            v-for="language in languages"
                            :key="language"
                            class="grid gap-1.5"
                        >
                            <Label :for="'track-' + language"
                                >{{ t('admin.academic.trackName') }} ·
                                {{ language.toUpperCase() }}</Label
                            >
                            <Input
                                :id="'track-' + language"
                                v-model="trackForm.name_i18n[language]"
                                :lang="language"
                                :dir="language === 'ar' ? 'rtl' : 'auto'"
                                required
                                maxlength="255"
                            />
                        </div>
                        <div class="flex items-center gap-2">
                            <Checkbox
                                id="track-active"
                                v-model="trackForm.active"
                            />
                            <Label for="track-active">{{
                                t('admin.academic.active')
                            }}</Label>
                        </div>
                        <Button type="submit" :disabled="trackForm.processing">
                            <PhPlus class="size-4" aria-hidden="true" />
                            {{ t('admin.academic.addTrack') }}
                        </Button>
                    </form>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>{{ t('admin.academic.periods') }}</CardTitle>
                    <p class="text-sm text-muted-foreground">
                        {{ t('admin.academic.periodsHelp') }}
                    </p>
                </CardHeader>
                <CardContent class="space-y-5">
                    <p
                        v-if="!windows.length"
                        class="text-sm text-muted-foreground"
                    >
                        {{ t('admin.academic.noWindows') }}
                    </p>
                    <div
                        v-for="window in windows"
                        :key="window.code"
                        class="flex flex-wrap items-center justify-between gap-3 border-b border-border py-3"
                    >
                        <div>
                            <p class="text-sm font-medium">
                                {{ windowLabel(window.code) }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                <bdi>{{ window.opens_at }}</bdi> —
                                <bdi>{{ window.closes_at }}</bdi>
                            </p>
                        </div>
                        <Badge variant="secondary">{{
                            window.active
                                ? t('admin.academic.active')
                                : t('admin.academic.inactive')
                        }}</Badge>
                    </div>
                    <form
                        class="grid gap-3 border-t border-border pt-4"
                        @submit.prevent="windowSubmit"
                    >
                        <h2 class="text-sm font-semibold">
                            {{ t('admin.academic.setPeriod') }}
                        </h2>
                        <p
                            v-if="windowForm.hasErrors"
                            role="alert"
                            class="text-sm text-destructive"
                        >
                            {{ t('admin.academic.validationError') }}
                        </p>
                        <div class="grid gap-1.5">
                            <Label for="window-code">{{
                                t('admin.academic.periodCode')
                            }}</Label>
                            <Select v-model="windowForm.window_code">
                                <SelectTrigger id="window-code" class="w-full"
                                    ><SelectValue
                                /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="code in allowedCodes"
                                        :key="code"
                                        :value="code"
                                        >{{ windowLabel(code) }}</SelectItem
                                    >
                                </SelectContent>
                            </Select>
                        </div>
                        <p class="text-xs text-muted-foreground">
                            {{ t('admin.academic.timezone') }} ·
                            <bdi>{{ edition.timezone }}</bdi>
                        </p>
                        <div class="grid gap-1.5">
                            <Label for="window-open">{{
                                t('admin.academic.opens')
                            }}</Label>
                            <Input
                                id="window-open"
                                v-model="windowForm.opens_at"
                                type="datetime-local"
                                required
                                :aria-invalid="!!windowForm.errors.opens_at"
                            />
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="window-close">{{
                                t('admin.academic.closes')
                            }}</Label>
                            <Input
                                id="window-close"
                                v-model="windowForm.closes_at"
                                type="datetime-local"
                                required
                                :aria-invalid="!!windowForm.errors.closes_at"
                            />
                        </div>
                        <div class="flex items-center gap-2">
                            <Checkbox
                                id="window-active"
                                v-model="windowForm.active"
                            />
                            <Label for="window-active">{{
                                t('admin.academic.active')
                            }}</Label>
                        </div>
                        <Button type="submit" :disabled="windowForm.processing">
                            {{ t('admin.academic.setPeriod') }}
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
