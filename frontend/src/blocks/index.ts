/**
 * Registry de bloques Puck
 * ─────────────────────────
 * Cada bloque exporta { render, config } donde:
 *  - render: componente React que renderiza el bloque
 *  - config: objeto Puck con { fields, defaultProps, label? }
 *
 * Para añadir un bloque nuevo:
 *  1. Crear `src/blocks/<NombreBloque>/index.tsx`
 *  2. Importarlo aquí y añadirlo al objeto `blocks`
 *  3. Añadirlo a la categoría correspondiente en `categories`
 */
import type { Config } from '@measured/puck';

import { Hero } from './Hero';
import { HeroSlider } from './HeroSlider';
import { TextBlock } from './TextBlock';
import { ImageText } from './ImageText';
import { Gallery } from './Gallery';
import { Features } from './Features';
import { PortfolioGrid } from './PortfolioGrid';
import { CTA } from './CTA';
import { Testimonials } from './Testimonials';
import { FAQ } from './FAQ';
import { Pricing } from './Pricing';
import { TeamGrid } from './TeamGrid';
import { ContactForm } from './ContactForm';
import { Stats } from './Stats';
import { Timeline } from './Timeline';
import { LogoGrid } from './LogoGrid';
import { VideoEmbed } from './VideoEmbed';
import { MapEmbed } from './MapEmbed';
import { Spacer } from './Spacer';
import { ImageBreak } from './ImageBreak';
import { Marquee } from './Marquee';
import { Newsletter } from './Newsletter';
import { ProductGrid } from './ProductGrid';
import { ProductFeatured } from './ProductFeatured';
import { CartMini } from './CartMini';
import { BookingForm } from './BookingForm';
import { ServicesList } from './ServicesList';
import { MemberGate } from './MemberGate';
import { MemberLoginForm } from './MemberLoginForm';

export const blocks = {
  Hero: Hero.config,
  HeroSlider: HeroSlider.config,
  TextBlock: TextBlock.config,
  ImageText: ImageText.config,
  Gallery: Gallery.config,
  Features: Features.config,
  PortfolioGrid: PortfolioGrid.config,
  CTA: CTA.config,
  Testimonials: Testimonials.config,
  FAQ: FAQ.config,
  Pricing: Pricing.config,
  TeamGrid: TeamGrid.config,
  ContactForm: ContactForm.config,
  Stats: Stats.config,
  Timeline: Timeline.config,
  LogoGrid: LogoGrid.config,
  VideoEmbed: VideoEmbed.config,
  MapEmbed: MapEmbed.config,
  Spacer: Spacer.config,
  ImageBreak: ImageBreak.config,
  Marquee: Marquee.config,
  Newsletter: Newsletter.config,
  ProductGrid: ProductGrid.config,
  ProductFeatured: ProductFeatured.config,
  CartMini: CartMini.config,
  BookingForm: BookingForm.config,
  ServicesList: ServicesList.config,
  MemberGate: MemberGate.config,
  MemberLoginForm: MemberLoginForm.config,
} as const;

export const puckConfig: Config = {
  components: blocks,
  categories: {
    Hero: { components: ['Hero', 'HeroSlider', 'ImageBreak', 'Marquee'] },
    Contenido: { components: ['TextBlock', 'ImageText', 'Gallery', 'VideoEmbed'] },
    Secciones: { components: ['Features', 'Stats', 'Timeline', 'LogoGrid', 'TeamGrid'] },
    Portfolio: { components: ['PortfolioGrid'] },
    Social: { components: ['Testimonials', 'Pricing', 'FAQ'] },
    Conversión: { components: ['CTA', 'ContactForm', 'Newsletter'] },
    Tienda: { components: ['ProductGrid', 'ProductFeatured', 'CartMini'] },
    Reservas: { components: ['BookingForm', 'ServicesList'] },
    Miembros: { components: ['MemberGate', 'MemberLoginForm'] },
    Utilidades: { components: ['Spacer', 'MapEmbed'] },
  },
  root: {
    fields: {
      title: { type: 'text' },
    },
  },
};

export type BlockName = keyof typeof blocks;
