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

const props = defineProps<{
    edition: { id: string; code: string; status: string };
    destinations: {
        id: string;
        code: string;
        label: string;
        bank: string;
        accountLast4: string;
        isDefault: boolean;
        active: boolean;
    }[];
    packages: {
        id: string;
        code: string;
        names: Record<string, string>;
        billingMode: string;
        price: string;
        currency: string;
        active: boolean;
    }[];
    destinationCreated: boolean;
    packageCreated: boolean;
}>();
const { t, locale } = useI18n();
const languages = ['id', 'en', 'ar'] as const;
const destinationForm = useForm({
    code: '',
    label: '',
    bank_name: '',
    account_number: '',
    account_holder: '',
    is_default: true,
    active: true,
    display_order: 0,
});
const packageForm = useForm({
    code: '',
    name_i18n: { id: '', en: '', ar: '' },
    billing_mode: 'FREE',
    price: '0',
    currency_code: 'IDR',
    payment_destination_id: null as string | null,
    active: true,
    display_order: 0,
});
const url = computed(() => '/admin/editions/' + props.edition.id);
const money = (amount: string, currency: string) =>
    new Intl.NumberFormat(locale.value, {
        style: 'currency',
        currency,
        maximumFractionDigits: 2,
    }).format(Number(amount));
const createDestination = () =>
    destinationForm.post(url.value + '/payment-destinations', {
        preserveScroll: true,
        onSuccess: () => destinationForm.reset(),
    });
const createPackage = () =>
    packageForm.post(url.value + '/packages', {
        preserveScroll: true,
        onSuccess: () => packageForm.reset(),
    });
</script>

<template>
    <Head :title="t('admin.configuration.title')" />
    <div class="flex w-full min-w-0 flex-col gap-6 p-4 md:p-8">
        <header class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <Link
                    href="/admin/editions"
                    class="admin-text-link text-sm text-muted-foreground"
                >
                    <PhArrowLeft
                        class="size-4 rtl:-scale-x-100"
                        aria-hidden="true"
                    />
                    {{ t('admin.configuration.back') }}
                </Link>
                <h1 class="mt-3 text-2xl font-semibold tracking-tight">
                    {{ t('admin.configuration.title') }} ·
                    <bdi>{{ edition.code }}</bdi>
                </h1>
                <p class="mt-2 text-sm text-muted-foreground">
                    {{ t('admin.configuration.description') }}
                </p>
            </div>
            <Badge variant="secondary">{{ edition.status }}</Badge>
        </header>

        <p
            v-if="destinationCreated || packageCreated"
            role="status"
            class="border border-primary/25 bg-primary/5 p-3 text-sm"
        >
            {{ t('admin.configuration.saved') }}
        </p>

        <div class="grid min-w-0 gap-6 xl:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle>{{
                        t('admin.configuration.destinations')
                    }}</CardTitle>
                    <p class="text-sm text-muted-foreground">
                        {{ t('admin.configuration.destinationHelp') }}
                    </p>
                </CardHeader>
                <CardContent class="space-y-5">
                    <p
                        v-if="destinations.length === 0"
                        class="text-sm text-muted-foreground"
                    >
                        {{ t('admin.configuration.noDestinations') }}
                    </p>
                    <div
                        v-for="item in destinations"
                        :key="item.id"
                        class="flex flex-wrap items-center justify-between gap-3 border-b border-border py-3"
                    >
                        <div>
                            <p class="text-sm font-medium">
                                <bdi>{{ item.code }}</bdi> · {{ item.label }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ item.bank }} · ****<bdi>{{
                                    item.accountLast4
                                }}</bdi>
                            </p>
                        </div>
                        <div class="flex gap-1">
                            <Badge v-if="item.isDefault" variant="outline">{{
                                t('admin.configuration.default')
                            }}</Badge>
                            <Badge variant="secondary">{{
                                item.active
                                    ? t('admin.configuration.active')
                                    : t('admin.configuration.inactive')
                            }}</Badge>
                        </div>
                    </div>

                    <form
                        class="grid gap-3 border-t border-border pt-4"
                        @submit.prevent="createDestination"
                    >
                        <h2 class="text-sm font-semibold">
                            {{ t('admin.configuration.createDestination') }}
                        </h2>
                        <p
                            v-if="destinationForm.hasErrors"
                            role="alert"
                            class="text-sm text-destructive"
                        >
                            {{ t('admin.configuration.validationError') }}
                        </p>
                        <div
                            v-for="field in [
                                'code',
                                'label',
                                'bank_name',
                                'account_number',
                                'account_holder',
                            ] as const"
                            :key="field"
                            class="grid gap-1.5"
                        >
                            <Label :for="'destination-' + field">{{
                                t('admin.configuration.fields.' + field)
                            }}</Label>
                            <Input
                                :id="'destination-' + field"
                                v-model="destinationForm[field]"
                                required
                                maxlength="255"
                                :aria-invalid="!!destinationForm.errors[field]"
                            />
                            <p
                                v-if="destinationForm.errors[field]"
                                class="text-xs text-destructive"
                            >
                                {{ destinationForm.errors[field] }}
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-4">
                            <div class="flex items-center gap-2">
                                <Checkbox
                                    id="destination-default"
                                    v-model="destinationForm.is_default"
                                />
                                <Label for="destination-default">{{
                                    t('admin.configuration.default')
                                }}</Label>
                            </div>
                            <div class="flex items-center gap-2">
                                <Checkbox
                                    id="destination-active"
                                    v-model="destinationForm.active"
                                />
                                <Label for="destination-active">{{
                                    t('admin.configuration.active')
                                }}</Label>
                            </div>
                        </div>
                        <Button
                            type="submit"
                            :disabled="destinationForm.processing"
                        >
                            <PhPlus class="size-4" aria-hidden="true" />
                            {{ t('admin.configuration.createDestination') }}
                        </Button>
                    </form>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>{{
                        t('admin.configuration.packages')
                    }}</CardTitle>
                    <p class="text-sm text-muted-foreground">
                        {{ t('admin.configuration.packageHelp') }}
                    </p>
                </CardHeader>
                <CardContent class="space-y-5">
                    <p
                        v-if="packages.length === 0"
                        class="text-sm text-muted-foreground"
                    >
                        {{ t('admin.configuration.noPackages') }}
                    </p>
                    <div
                        v-for="item in packages"
                        :key="item.id"
                        class="flex flex-wrap items-center justify-between gap-3 border-b border-border py-3"
                    >
                        <div>
                            <p class="text-sm font-medium">
                                <bdi>{{ item.code }}</bdi> ·
                                {{
                                    item.names[locale] ??
                                    item.names.en ??
                                    item.names.id
                                }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ item.billingMode }} ·
                                <bdi>{{
                                    money(item.price, item.currency)
                                }}</bdi>
                            </p>
                        </div>
                        <Badge variant="secondary">{{
                            item.active
                                ? t('admin.configuration.active')
                                : t('admin.configuration.inactive')
                        }}</Badge>
                    </div>

                    <form
                        class="grid gap-3 border-t border-border pt-4"
                        @submit.prevent="createPackage"
                    >
                        <h2 class="text-sm font-semibold">
                            {{ t('admin.configuration.createPackage') }}
                        </h2>
                        <p
                            v-if="packageForm.hasErrors"
                            role="alert"
                            class="text-sm text-destructive"
                        >
                            {{ t('admin.configuration.validationError') }}
                        </p>
                        <div class="grid gap-1.5">
                            <Label for="package-code">{{
                                t('admin.configuration.fields.code')
                            }}</Label>
                            <Input
                                id="package-code"
                                v-model="packageForm.code"
                                required
                                maxlength="255"
                                :aria-invalid="!!packageForm.errors.code"
                            />
                        </div>
                        <div
                            v-for="language in languages"
                            :key="language"
                            class="grid gap-1.5"
                        >
                            <Label :for="'package-name-' + language">
                                {{
                                    t('admin.configuration.fields.package_name')
                                }}
                                · {{ language.toUpperCase() }}
                            </Label>
                            <Input
                                :id="'package-name-' + language"
                                v-model="packageForm.name_i18n[language]"
                                :lang="language"
                                :dir="language === 'ar' ? 'rtl' : 'auto'"
                                required
                                maxlength="255"
                                :aria-invalid="
                                    !!packageForm.errors[
                                        `name_i18n.${language}`
                                    ]
                                "
                            />
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="package-billing">{{
                                t('admin.configuration.fields.billing_mode')
                            }}</Label>
                            <Select v-model="packageForm.billing_mode">
                                <SelectTrigger
                                    id="package-billing"
                                    class="w-full"
                                    ><SelectValue
                                /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="FREE">FREE</SelectItem>
                                    <SelectItem value="PAID">PAID</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div
                            v-if="packageForm.billing_mode === 'PAID'"
                            class="grid gap-3"
                        >
                            <p class="text-sm text-muted-foreground">
                                {{ t('admin.configuration.paidNote') }}
                            </p>
                            <div class="grid gap-1.5">
                                <Label for="package-price">{{
                                    t('admin.configuration.fields.price')
                                }}</Label>
                                <Input
                                    id="package-price"
                                    v-model="packageForm.price"
                                    type="number"
                                    min="0.01"
                                    step="0.01"
                                    required
                                />
                            </div>
                            <div class="grid gap-1.5">
                                <Label for="package-currency">{{
                                    t(
                                        'admin.configuration.fields.currency_code',
                                    )
                                }}</Label>
                                <Input
                                    id="package-currency"
                                    v-model="packageForm.currency_code"
                                    maxlength="3"
                                    required
                                />
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <Checkbox
                                id="package-active"
                                v-model="packageForm.active"
                            />
                            <Label for="package-active">{{
                                t('admin.configuration.active')
                            }}</Label>
                        </div>
                        <Button
                            type="submit"
                            :disabled="packageForm.processing"
                        >
                            <PhPlus class="size-4" aria-hidden="true" />
                            {{ t('admin.configuration.createPackage') }}
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
