/**
 * Templates de arranque para nuevos sitios.
 * Cada template es un snapshot JSON de una página completa Puck.
 *
 * Al crear un nuevo cliente/sitio, el cliente elige un template
 * y se crea la página de inicio + páginas secundarias pre-rellenadas.
 */
import corporate from './corporate.json';
import portfolioPhotographer from './portfolio-photographer.json';
import creativeStudio from './creative-studio.json';
import architectureStudio from './architecture-studio.json';
import legalFirm from './legal-firm.json';
import clinic from './clinic.json';
import interiorDesigner from './interior-designer.json';
import ecommerceFashion from './ecommerce-fashion.json';
import ecommerceProduct from './ecommerce-product.json';
import hotelBoutique from './hotel-boutique.json';
import restaurant from './restaurant.json';
import eventLanding from './event-landing.json';
import comingSoon from './coming-soon.json';
import blogMagazine from './blog-magazine.json';
import agency from './agency.json';

export interface Template {
  id: string;
  name: string;
  description: string;
  category: 'corporate' | 'portfolio' | 'ecommerce' | 'hospitality' | 'landing' | 'blog';
  thumbnail: string;
  pages: Record<string, any>; // slug -> puck data
}

export const templates: Template[] = [
  corporate as Template,
  agency as Template,
  legalFirm as Template,
  clinic as Template,
  creativeStudio as Template,
  portfolioPhotographer as Template,
  architectureStudio as Template,
  interiorDesigner as Template,
  ecommerceFashion as Template,
  ecommerceProduct as Template,
  hotelBoutique as Template,
  restaurant as Template,
  eventLanding as Template,
  comingSoon as Template,
  blogMagazine as Template,
];

export const templateCategories = {
  corporate: 'Corporativa',
  portfolio: 'Portfolio',
  ecommerce: 'Tienda online',
  hospitality: 'Hostelería',
  landing: 'Landing / One-page',
  blog: 'Blog / Magazine',
};
