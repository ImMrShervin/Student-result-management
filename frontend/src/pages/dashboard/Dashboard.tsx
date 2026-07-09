import { useQuery } from '@tanstack/react-query';
import { api } from '@/lib/api';
import { useTranslation } from 'react-i18next';
import { useAuth } from '@/stores/auth';
import { Users, GraduationCap, BookOpen, Building2, ClipboardList, Award, School } from 'lucide-react';
import { Bar } from 'react-chartjs-2';
import { Chart as ChartJS, ArcElement, BarElement, CategoryScale, LinearScale, Tooltip, Legend } from 'chart.js';

ChartJS.register(ArcElement, BarElement, CategoryScale, LinearScale, Tooltip, Legend);

export default function Dashboard() {
  const { t } = useTranslation();
  const user = useAuth((s) => s.user);
  const { data, isLoading } = useQuery({
    queryKey: ['dashboard'],
    queryFn: async () => (await api.get('/dashboard')).data,
  });

  if (isLoading) return <div className="skeleton h-96" />;

  const stats = data?.stats ?? {};
  const cards = [
    { label: 'stats.students', value: stats.students, icon: Users, color: 'blue' },
    { label: 'stats.teachers', value: stats.teachers, icon: GraduationCap, color: 'green' },
    { label: 'stats.courses', value: stats.courses, icon: BookOpen, color: 'purple' },
    { label: 'stats.faculties', value: stats.faculties, icon: Building2, color: 'orange' },
    { label: 'stats.departments', value: stats.departments, icon: School, color: 'pink' },
    { label: 'stats.enrollments', value: stats.enrollments, icon: ClipboardList, color: 'indigo' },
    { label: 'stats.published_grades', value: stats.published_grades, icon: Award, color: 'red' },
  ].filter((c) => c.value !== undefined);

  const gradeDist = data?.grade_distribution ?? [];
  const avgByDept = data?.avg_gpa_by_department ?? [];

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-bold">{t('dashboard.welcome')}, {user?.first_name} 👋</h1>
        <p className="text-slate-500 dark:text-slate-400">{t('dashboard.overview')}</p>
      </div>

      {cards.length > 0 && (
        <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
          {cards.map((c) => (
            <div key={c.label} className="card">
              <c.icon className={`w-6 h-6 mb-2 text-${c.color}-500`} />
              <div className="text-2xl font-bold">{c.value?.toLocaleString?.() ?? c.value}</div>
              <div className="text-xs text-slate-500">{t(c.label)}</div>
            </div>
          ))}
        </div>
      )}

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {gradeDist.length > 0 && (
          <div className="card">
            <h3 className="font-semibold mb-4">{t('dashboard.grade_distribution')}</h3>
            <Bar
              data={{
                labels: gradeDist.map((g: any) => g.letter_grade),
                datasets: [{ data: gradeDist.map((g: any) => g.c), backgroundColor: '#3b82f6', borderRadius: 6 }],
              }}
              options={{ plugins: { legend: { display: false } }, responsive: true }}
            />
          </div>
        )}

        {avgByDept.length > 0 && (
          <div className="card">
            <h3 className="font-semibold mb-4">{t('dashboard.avg_gpa_by_department')}</h3>
            <Bar
              data={{
                labels: avgByDept.map((d: any) => d.name),
                datasets: [{ label: 'Avg GPA', data: avgByDept.map((d: any) => d.avg_gpa), backgroundColor: '#10b981', borderRadius: 6 }],
              }}
              options={{ indexAxis: 'y' as const, plugins: { legend: { display: false } } }}
            />
          </div>
        )}
      </div>

      {data?.top_students?.length > 0 && (
        <div className="card">
          <h3 className="font-semibold mb-4">{t('dashboard.top_students')}</h3>
          <div className="overflow-x-auto">
            <table className="table">
              <thead><tr><th>#</th><th>Name</th><th>Department</th><th>GPA</th></tr></thead>
              <tbody>
                {data.top_students.map((s: any, i: number) => (
                  <tr key={s.id}>
                    <td className="font-mono">{i + 1}</td>
                    <td>{s.user?.first_name} {s.user?.last_name}</td>
                    <td>{s.department?.name}</td>
                    <td><span className="badge bg-emerald-100 text-emerald-800">{Number(s.cumulative_gpa).toFixed(2)}</span></td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {data?.student && (
        <div className="card">
          <h3 className="font-semibold mb-4">Your Academic Summary</h3>
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
            <StatMini label="Student #" value={data.student.student_number} />
            <StatMini label="Current GPA" value={Number(data.student.current_gpa).toFixed(2)} />
            <StatMini label="Cumulative GPA" value={Number(data.student.cumulative_gpa).toFixed(2)} />
            <StatMini label="Credits Passed" value={data.student.credits_passed} />
          </div>
        </div>
      )}
    </div>
  );
}

function StatMini({ label, value }: { label: string; value: any }) {
  return (
    <div className="p-3 bg-slate-50 dark:bg-slate-700/40 rounded-lg">
      <div className="text-xs text-slate-500">{label}</div>
      <div className="text-lg font-semibold">{value}</div>
    </div>
  );
}
