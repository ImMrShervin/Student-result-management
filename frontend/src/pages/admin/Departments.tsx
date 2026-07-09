import { useQuery } from '@tanstack/react-query';
import { api } from '@/lib/api';
import { DataTable } from '@/components/ui/DataTable';

export default function Departments() {
  const { data, isLoading } = useQuery({
    queryKey: ['departments'],
    queryFn: async () => (await api.get('/departments')).data,
  });
  return (
    <div className="space-y-4">
      <h1 className="text-2xl font-bold">Departments</h1>
      <DataTable
        loading={isLoading}
        rows={data?.data ?? []}
        columns={[
          { key: 'code', header: 'Code' },
          { key: 'name', header: 'Name' },
          { key: 'faculty', header: 'Faculty', render: (r: any) => r.faculty?.name },
          { key: 'description', header: 'Description' },
        ]}
      />
    </div>
  );
}
