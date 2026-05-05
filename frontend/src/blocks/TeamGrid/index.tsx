import type { ComponentConfig } from '@measured/puck';
import { Linkedin, Mail } from 'lucide-react';

export type TeamMember = {
  name: string;
  role: string;
  photo: string;
  bio?: string;
  linkedin?: string;
  email?: string;
};

export type TeamGridProps = {
  heading?: string;
  subtitle?: string;
  members: TeamMember[];
  columns: 2 | 3 | 4;
  shape: 'square' | 'circle';
};

const colsMap = {
  2: 'md:grid-cols-2',
  3: 'md:grid-cols-2 lg:grid-cols-3',
  4: 'md:grid-cols-2 lg:grid-cols-4',
};

const TeamGridRender = ({ heading, subtitle, members, columns, shape }: TeamGridProps) => {
  const shapeCls = shape === 'circle' ? 'rounded-full aspect-square' : 'aspect-[3/4]';
  return (
    <section className="w-full py-24 px-6 bg-white">
      <div className="max-w-7xl mx-auto">
        {heading && (
          <h2 className="font-serif text-3xl md:text-5xl font-light mb-4 text-center leading-tight text-neutral-900">
            {heading}
          </h2>
        )}
        {subtitle && (
          <p className="text-base text-neutral-600 text-center max-w-2xl mx-auto mb-16 leading-relaxed">
            {subtitle}
          </p>
        )}
        <div className={`grid grid-cols-1 gap-10 ${colsMap[columns]}`}>
          {members.map((m, i) => (
            <div key={i} className="flex flex-col">
              <div className={`overflow-hidden mb-4 bg-neutral-100 ${shapeCls}`}>
                <img
                  src={m.photo}
                  alt={m.name}
                  className="w-full h-full object-cover transition hover:scale-105 duration-700"
                />
              </div>
              <h3 className="font-serif text-xl text-neutral-900 mb-1">{m.name}</h3>
              <p className="text-xs tracking-[0.2em] uppercase text-neutral-500 mb-3">{m.role}</p>
              {m.bio && <p className="text-sm text-neutral-600 leading-relaxed mb-4">{m.bio}</p>}
              <div className="flex gap-3 text-neutral-500">
                {m.linkedin && (
                  <a
                    href={m.linkedin}
                    className="hover:text-neutral-900 transition"
                    aria-label={`LinkedIn de ${m.name}`}
                  >
                    <Linkedin size={16} />
                  </a>
                )}
                {m.email && (
                  <a
                    href={`mailto:${m.email}`}
                    className="hover:text-neutral-900 transition"
                    aria-label={`Email de ${m.name}`}
                  >
                    <Mail size={16} />
                  </a>
                )}
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export const TeamGrid: { config: ComponentConfig<TeamGridProps> } = {
  config: {
    label: 'Equipo',
    fields: {
      heading: { type: 'text', label: 'Título' },
      subtitle: { type: 'textarea', label: 'Subtítulo' },
      members: {
        type: 'array',
        label: 'Miembros',
        arrayFields: {
          name: { type: 'text', label: 'Nombre' },
          role: { type: 'text', label: 'Cargo' },
          photo: { type: 'text', label: 'Foto (URL)' },
          bio: { type: 'textarea', label: 'Biografía breve' },
          linkedin: { type: 'text', label: 'URL LinkedIn' },
          email: { type: 'text', label: 'Email' },
        },
      },
      columns: {
        type: 'select',
        label: 'Columnas',
        options: [
          { label: '2 columnas', value: 2 },
          { label: '3 columnas', value: 3 },
          { label: '4 columnas', value: 4 },
        ],
      },
      shape: {
        type: 'radio',
        label: 'Forma de la foto',
        options: [
          { label: 'Cuadrada', value: 'square' },
          { label: 'Círculo', value: 'circle' },
        ],
      },
    },
    defaultProps: {
      heading: 'Nuestro equipo',
      subtitle: 'Personas con talento y pasión por lo que hacen.',
      columns: 4,
      shape: 'square',
      members: [
        {
          name: 'Laura Fernández',
          role: 'Creative Director',
          photo: 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=600&q=80',
          bio: 'Más de 15 años dirigiendo proyectos para marcas de lujo.',
          linkedin: '#',
          email: 'laura@hawkins.com',
        },
        {
          name: 'Javier Moreno',
          role: 'Head of Strategy',
          photo: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=600&q=80',
          bio: 'Estratega obsesionado con el detalle y el dato.',
          linkedin: '#',
          email: 'javier@hawkins.com',
        },
        {
          name: 'Sofía Herrera',
          role: 'Lead Designer',
          photo: 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=600&q=80',
          bio: 'Diseñadora multidisciplinar con mirada editorial.',
          linkedin: '#',
          email: 'sofia@hawkins.com',
        },
        {
          name: 'Daniel Torres',
          role: 'Tech Lead',
          photo: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=600&q=80',
          bio: 'Arquitecto de software apasionado por el performance.',
          linkedin: '#',
          email: 'daniel@hawkins.com',
        },
      ],
    },
    render: TeamGridRender,
  },
};
