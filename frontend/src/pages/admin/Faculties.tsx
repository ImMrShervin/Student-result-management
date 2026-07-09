import { useQuery } from '@tanstack/react-query';
import { api } from '@/lib/api';
import { DataTable } from '@/components/ui/DataTable';

export default function Faculties() {
  const { data, isLoading } = useQuery({
    queryKey: ['faculties'],
    queryFn: async () => (await api.get('/faculties')).data,
  });
  return (
    <div className="space-y-4">
      <h1 className="text-2xl font-bold">Faculties</h1>
      <DataTable
        loading={isLoading}
        rows={data?.data ?? []}
        columns={[
          { key: 'code', header: 'Code' },
          { key: 'name', header: 'Name' },
          { key: 'departments', header: 'Departments', render: (r: any) => r.departments_count ?? '—' },
          { key: 'description', header: 'Description' },
        ]}
      />
    </div>
  );
}
