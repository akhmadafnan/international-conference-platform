<script setup lang="ts">
import { computed } from 'vue'
import { Globe2 } from '@lucide/vue'
import { useI18n } from 'vue-i18n'
import {
  getLocaleDirection,
  supportedLocales,
  type SupportedLocale,
} from '@/i18n'

const { locale, t } = useI18n()

const direction = computed(() => getLocaleDirection(locale.value))

function selectLocale(nextLocale: SupportedLocale) {
  locale.value = nextLocale
}
</script>

<template>
  <main class="min-h-screen bg-background text-foreground">
    <section class="mx-auto flex min-h-screen w-full max-w-6xl items-center px-6 py-16 lg:px-10">
      <div class="w-full border-y border-border py-12 md:py-16">
        <div class="mb-10 flex flex-wrap items-center justify-between gap-5">
          <div class="flex items-center gap-3 text-sm font-medium text-muted-foreground">
            <span class="flex size-9 items-center justify-center rounded-full border border-border bg-surface">
              <Globe2 class="size-4" aria-hidden="true" />
            </span>
            <span>ICHES</span>
          </div>

          <div
            class="flex items-center gap-1 rounded-full border border-border bg-surface p-1"
            :aria-label="t('locale.label')"
          >
            <button
              v-for="item in supportedLocales"
              :key="item"
              type="button"
              class="rounded-full px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.08em] transition-colors"
              :class="
                locale === item
                  ? 'bg-primary text-primary-foreground'
                  : 'text-muted-foreground hover:text-foreground'
              "
              :aria-pressed="locale === item"
              @click="selectLocale(item)"
            >
              {{ item }}
            </button>
          </div>
        </div>

        <p class="mb-5 text-xs font-semibold uppercase tracking-[0.18em] text-primary">
          {{ t('prototype.eyebrow') }}
        </p>

        <h1 class="max-w-4xl text-balance text-4xl font-semibold tracking-[-0.035em] sm:text-5xl lg:text-7xl">
          {{ t('prototype.title') }}
        </h1>

        <p class="mt-7 max-w-2xl text-pretty text-base leading-7 text-muted-foreground md:text-lg">
          {{ t('prototype.description') }}
        </p>

        <dl class="mt-12 grid gap-px border border-border bg-border sm:grid-cols-3">
          <div class="bg-background p-5">
            <dt class="text-xs font-medium uppercase tracking-[0.12em] text-muted-foreground">
              {{ t('prototype.branch') }}
            </dt>
            <dd class="mt-2 font-mono text-sm">proto/public-frontend-v1</dd>
          </div>
          <div class="bg-background p-5">
            <dt class="text-xs font-medium uppercase tracking-[0.12em] text-muted-foreground">
              {{ t('prototype.phase') }}
            </dt>
            <dd class="mt-2 text-sm font-medium">{{ t('prototype.phaseValue') }}</dd>
          </div>
          <div class="bg-background p-5">
            <dt class="text-xs font-medium uppercase tracking-[0.12em] text-muted-foreground">
              {{ t('prototype.direction') }}
            </dt>
            <dd class="mt-2 font-mono text-sm uppercase">{{ direction }}</dd>
          </div>
        </dl>
      </div>
    </section>
  </main>
</template>
