import type { ComponentConfig } from '@measured/puck';

export type SpacerProps = {
  height: 'xs' | 'sm' | 'md' | 'lg' | 'xl' | 'xxl';
  showDivider: boolean;
  dividerStyle: 'line' | 'dot';
};

const heightMap: Record<SpacerProps['height'], string> = {
  xs: 'h-6',
  sm: 'h-12',
  md: 'h-20',
  lg: 'h-32',
  xl: 'h-48',
  xxl: 'h-64',
};

const SpacerRender = ({ height, showDivider, dividerStyle }: SpacerProps) => {
  return (
    <div className={`w-full ${heightMap[height]} flex items-center justify-center`}>
      {showDivider && dividerStyle === 'line' && (
        <div className="w-24 h-px bg-black/20" />
      )}
      {showDivider && dividerStyle === 'dot' && (
        <div className="flex items-center gap-2">
          <span className="w-1 h-1 rounded-full bg-black/30" />
          <span className="w-1 h-1 rounded-full bg-black/30" />
          <span className="w-1 h-1 rounded-full bg-black/30" />
        </div>
      )}
    </div>
  );
};

export const Spacer: { config: ComponentConfig<SpacerProps> } = {
  config: {
    label: 'Spacer',
    fields: {
      height: {
        type: 'select',
        label: 'Altura',
        options: [
          { label: 'XS', value: 'xs' },
          { label: 'SM', value: 'sm' },
          { label: 'MD', value: 'md' },
          { label: 'LG', value: 'lg' },
          { label: 'XL', value: 'xl' },
          { label: 'XXL', value: 'xxl' },
        ],
      },
      showDivider: {
        type: 'radio',
        label: 'Mostrar divisor',
        options: [
          { label: 'Sí', value: true },
          { label: 'No', value: false },
        ],
      },
      dividerStyle: {
        type: 'radio',
        label: 'Estilo divisor',
        options: [
          { label: 'Línea', value: 'line' },
          { label: 'Puntos', value: 'dot' },
        ],
      },
    },
    defaultProps: {
      height: 'lg',
      showDivider: true,
      dividerStyle: 'line',
    },
    render: SpacerRender,
  },
};
