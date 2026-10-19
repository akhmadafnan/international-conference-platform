<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    PhArrowRight,
    PhCheckCircle,
    PhClock,
    PhTicket,
} from '@phosphor-icons/vue';
import { useI18n } from 'vue-i18n';
import { dashboard } from '@/routes';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';

type RegistrationSummary = {
    id: string;
    code: string;
    status: string;
    packageName: string;
    billingMode: string;
    feeAmount: string;
    currencyCode: string;
    complimentary: boolean;
    nextAction: string;
    nextActionUrl: string | null;
    registrationUrl: string;
    eventPass: {
        showUrl: string;
    } | null;
};

defineProps<{
    phase02: {
        registration: RegistrationSummary | null;
    };
}>();

const { t, te } = useI18n();
const actionLabel = (key: string) =>
    te(`wizardUx.actions.${key}`)
        ? t(`wizardUx.actions.${key}`)
        : t(`phase02.nextAction.${key}`);

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});
</script>

<template>
    <Head :title="t('navigation.dashboard')" />

    <div class="participant-page flex w-full flex-col gap-6">
        <header>
            <p class="text-sm font-medium text-muted-foreground">
                {{ t('phase02.dashboard.eyebrow') }}
            </p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight">
                {{ t('phase02.dashboard.title') }}
            </h1>
            <p class="mt-1 max-w-2xl text-sm text-muted-foreground">
                {{ t('phase02.dashboard.description') }}
            </p>
        </header>

        <Card
            v-if="phase02.registration"
            class="p-4"
            aria-labelledby="next-action-heading"
        >
            <p
                class="text-xs font-semibold tracking-wider text-muted-foreground uppercase"
            >
                {{ t('phase02.nextAction.label') }}
            </p>
            <h2 id="next-action-heading" class="mt-2 text-xl font-semibold">
                {{ actionLabel(phase02.registration.nextAction) }}
            </h2>

            <div class="mt-5 flex flex-wrap gap-3">
                <Button v-if="phase02.registration.nextActionUrl" as-child>
                    <Link :href="phase02.registration.nextActionUrl!">
                        {{ actionLabel(phase02.registration.nextAction) }}
                        <PhArrowRight
                            class="size-4 rtl:rotate-180"
                            aria-hidden="true"
                        />
                    </Link>
                </Button>

                <Button
                    v-if="phase02.registration.eventPass"
                    as-child
                    variant="outline"
                >
                    <Link :href="phase02.registration.eventPass.showUrl">
                        <PhTicket class="size-4" aria-hidden="true" />
                        {{ t('phase02.eventPass.open') }}
                    </Link>
                </Button>
            </div>
        </Card>

        <section v-if="phase02.registration" class="grid gap-4 sm:grid-cols-2">
            <Card class="p-4">
                <div class="flex items-center gap-2">
                    <PhCheckCircle
                        v-if="phase02.registration.status === 'CONFIRMED'"
                        class="size-5"
                    />
                    <PhClock v-else class="size-5" aria-hidden="true" />
                    <h2 class="font-semibold">
                        {{ t('phase02.dashboard.registration') }}
                    </h2>
                </div>
                <p
                    dir="ltr"
                    class="mt-4 text-start font-mono text-sm font-semibold"
                >
                    {{ phase02.registration.code }}
                </p>
                <p class="mt-1 text-sm text-muted-foreground">
                    {{ phase02.registration.packageName }}
                </p>
                <p class="mt-3 text-sm font-medium">
                    {{
                        t(
                            `phase02.registrationStatus.${phase02.registration.status}`,
                        )
                    }}
                </p>
            </Card>

            <Card class="p-4">
                <div class="flex items-center gap-2">
                    <PhTicket class="size-5" aria-hidden="true" />
                    <h2 class="font-semibold">
                        {{ t('phase02.eventPass.title') }}
                    </h2>
                </div>
                <p class="mt-4 text-sm text-muted-foreground">
                    {{
                        phase02.registration.eventPass
                            ? t('phase02.dashboard.eventPassReady')
                            : t('phase02.dashboard.eventPassPending')
                    }}
                </p>
            </Card>
        </section>

        <Card v-else class="border-dashed p-4 text-center">
            <h2 class="text-lg font-semibold">
                {{ t('phase02.dashboard.noRegistration') }}
            </h2>
            <p class="mx-auto mt-2 max-w-xl text-sm text-muted-foreground">
                {{ t('phase02.dashboard.noRegistrationHelp') }}
            </p>
        </Card>
    </div>
</template>
