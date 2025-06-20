<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class BooksTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('books')->insert([
            [
                'id' => 1,
                'title' => "MANAGING YOUR FINANCES GOD'S WAY",
                'print_date' => '2025-05-19',
                'unit_cost' => 1200,
                'isbn' => 781,
                'description' => "The approach used in this book seeks to help the reader see that by changing their thinking and adopting a new look towards their financial position they could change their life for the better. This award winning book is formulated in 10 easy to follow steps. It is very easy read addressed to a keen newcomer but also valuable to the seasoned investor. This book will not only give the knowledge of how to handle money but also help you understand the spiritual side of money.",
                'created_at' => '2025-05-20 15:24:15',
                'updated_at' => '2025-05-21 07:42:51',
                'reorder_level' => 20,
            ],
            [
                'id' => 2,
                'title' => 'The Financial Planner',
                'print_date' => '2025-05-20',
                'unit_cost' => 1200,
                'isbn' => 230,
                'description' => 'The Financial Planner is a powerful tool designed to help you take control of your finances with clarity and purpose. It contains sections for budgeting, tracking expenses, setting financial goals, building smart money habits, amongst others, supported by practical tips, worksheets, and tools to ensure practical application. Its straightforward, actionable, and tailored to help you achieve your financial goals at your own pace.',
                'created_at' => '2025-05-21 07:52:30',
                'updated_at' => '2025-05-21 07:52:30',
                'reorder_level' => 20,
            ],
            [
                'id' => 3,
                'title' => 'Financial Reflections',
                'print_date' => '2025-05-20',
                'unit_cost' => 1000,
                'isbn' => 145,
                'description' => "Financial Reflections is more than just a book-it's a roadmap to financial confidence and success. It is a practical guide to mastering your finances with clarity and purpose. Whether you're looking to break free from financial stress or take your wealth to the next level, this book equips you with the confidence and strategies to shape your financial future.\r\nBased on the book, we launched a 6-month guided program that provides structured learning, accountability, and expert insights to help you take full control of your financial future.",
                'created_at' => '2025-05-21 07:58:00',
                'updated_at' => '2025-05-21 07:58:00',
                'reorder_level' => 20,
            ],
            [
                'id' => 4,
                'title' => "MANAGING YOUR FINANCES GOD'S WAY- The WorkBook",
                'print_date' => '2025-05-20',
                'unit_cost' => 850,
                'isbn' => 777,
                'description' => 'This workbook draws from its "mother", MANAGING YOUR FINANCES GOD\'S WAY- A 10 step Personal Finance Manual. Scripture used all through each chapter serves as a basis for grounding the lessons in God\'s will and purpose. The workbook specifically seeks to transform the readers\' financial reality by using real-life stories that the reader will identify with. \r\nIt is applicable and practically addresses everyday financial concerns. Although this workbook can seamlessly be used individually, group work is encouraged.',
                'created_at' => '2025-05-21 08:02:32',
                'updated_at' => '2025-05-21 08:02:32',
                'reorder_level' => 20,
            ],
            [
                'id' => 5,
                'title' => 'Smart Money Habits - 5 habits to financial Success',
                'print_date' => '2025-05-20',
                'unit_cost' => 950,
                'isbn' => null,
                'description' => 'Many people know what they ought to be doing with their money yet they often do not do it. Through simple habits, this book shows you how you can surpass your financial anxieties and come within reach of your dreams and goals. Smart Money Habits is practical, forthright, helpful and fun to read! It was written so that readers can quickly and easily comprehend exactly what actions they need to take and why. It provides solutions to the problems at hand and the problem ahead.',
                'created_at' => '2025-05-21 08:06:07',
                'updated_at' => '2025-05-21 08:06:07',
                'reorder_level' => 20,
            ],
            [
                'id' => 6,
                'title' => 'Sarah and Alpha -Money and Other Lessons for the Young Investor',
                'print_date' => '2025-05-20',
                'unit_cost' => 1000,
                'isbn' => null,
                'description' => 'Inspired by the growing need for a personal finance book for young readers, Sarah and Alpha is designed to instill financial literacy from an early age. Set within a family environment, it features 15 easy-to-read, engaging stories that teach essential lessons about money and other essential life-skills. \r\nWith relatable characters and practical insights, this book makes financial education fun, accessible, and impactful helping young readers build smart money habits that will serve them for life',
                'created_at' => '2025-05-21 08:08:21',
                'updated_at' => '2025-05-21 08:08:21',
                'reorder_level' => 20,
            ],
        ]);
    }
}

