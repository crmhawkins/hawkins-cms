/**
 * Gestor de módulos opcionales.
 * El cliente activa/desactiva cada uno desde Settings en el admin.
 */
import { cms } from './cms';

export type ModuleName = 'ecommerce' | 'newsletter' | 'booking' | 'members';

export interface ModuleStatus {
  ecommerce: boolean;
  newsletter: boolean;
  booking: boolean;
  members: boolean;
}

export async function getModules(): Promise<ModuleStatus> {
  const s = await cms.getSettings();
  return {
    ecommerce: (s as any)?.module_ecommerce_enabled ?? false,
    newsletter: (s as any)?.module_newsletter_enabled ?? false,
    booking: (s as any)?.module_booking_enabled ?? false,
    members: (s as any)?.module_members_enabled ?? false,
  };
}

export async function isModuleEnabled(name: ModuleName): Promise<boolean> {
  const mods = await getModules();
  return mods[name];
}

/** Throws 404 si el módulo está desactivado. Usar en route handlers. */
export async function requireModule(name: ModuleName): Promise<void> {
  const enabled = await isModuleEnabled(name);
  if (!enabled) {
    const { notFound } = await import('next/navigation');
    notFound();
  }
}
